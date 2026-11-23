<?php

namespace App\Support;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

/**
 * Baca & tulis file Excel (.xlsx) sederhana tanpa library tambahan.
 * - read(): mengambil isi sheet pertama sebagai array baris (juga mendukung .csv)
 * - write(): membuat file .xlsx (dipakai untuk template impor)
 */
class SimpleXlsx
{
    private const NS_REL = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    /** @return array<int, array<int, string>> */
    public static function read(string $path, string $extension): array
    {
        return strtolower($extension) === 'csv' ? self::readCsv($path) : self::readXlsx($path);
    }

    /* ------------------------------------------------------------------ */

    private static function readCsv(string $path): array
    {
        $content = file_get_contents($path);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);              // buang BOM
        if (! mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        // Excel versi Indonesia biasanya memakai titik koma (;)
        $firstLine = strtok($content, "\r\n") ?: '';
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        $rows = [];
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $content);
        rewind($handle);
        while (($row = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
            $rows[] = array_map(fn ($v) => trim((string) $v), $row);
        }
        fclose($handle);

        return $rows;
    }

    private static function readXlsx(string $path): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP "zip" belum aktif. Aktifkan extension=zip di php.ini, atau unggah file CSV.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('File Excel tidak bisa dibuka. Pastikan formatnya .xlsx.');
        }

        try {
            $shared = self::sharedStrings($zip);
            $sheetXml = $zip->getFromName(self::firstSheetPath($zip));
            if ($sheetXml === false) {
                throw new RuntimeException('Sheet pertama pada file Excel tidak ditemukan.');
            }

            $sheet = new SimpleXMLElement($sheetXml);
            $rows = [];
            foreach ($sheet->sheetData->row as $row) {
                $cells = [];
                foreach ($row->c as $cell) {
                    $index = self::columnIndex((string) $cell['r']);
                    $cells[$index] = self::cellValue($cell, $shared);
                }
                if ($cells) {
                    $max = max(array_keys($cells));
                    $line = [];
                    for ($i = 0; $i <= $max; $i++) {
                        $line[] = $cells[$i] ?? '';
                    }
                    $rows[(int) $row['r'] - 1] = $line;
                }
            }
            ksort($rows);

            return array_values($rows);
        } finally {
            $zip->close();
        }
    }

    private static function sharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }
        $strings = [];
        foreach ((new SimpleXMLElement($xml))->si as $si) {
            $text = (string) $si->t;
            foreach ($si->r as $run) {                     // teks dengan format (rich text)
                $text .= (string) $run->t;
            }
            $strings[] = $text;
        }

        return $strings;
    }

    private static function firstSheetPath(ZipArchive $zip): string
    {
        $workbook = $zip->getFromName('xl/workbook.xml');
        $rels = $zip->getFromName('xl/_rels/workbook.xml.rels');
        if ($workbook === false || $rels === false) {
            return 'xl/worksheets/sheet1.xml';
        }

        $first = (new SimpleXMLElement($workbook))->sheets->sheet[0];
        $id = (string) $first->attributes(self::NS_REL)['id'];

        foreach ((new SimpleXMLElement($rels))->Relationship as $rel) {
            if ((string) $rel['Id'] === $id) {
                $target = ltrim((string) $rel['Target'], '/');

                return str_starts_with($target, 'xl/') ? $target : 'xl/'.$target;
            }
        }

        return 'xl/worksheets/sheet1.xml';
    }

    private static function cellValue(SimpleXMLElement $cell, array $shared): string
    {
        $type = (string) $cell['t'];
        if ($type === 's') {
            return trim($shared[(int) $cell->v] ?? '');
        }
        if ($type === 'inlineStr') {
            return trim((string) $cell->is->t);
        }

        $value = trim((string) $cell->v);
        // Angka panjang (mis. NIM) kadang tersimpan sebagai 2.21402001E+8
        if ($value !== '' && is_numeric($value) && stripos($value, 'e') !== false) {
            $value = sprintf('%.0f', (float) $value);
        }
        // 221402001.0 -> 221402001
        if (preg_match('/^\d+\.0+$/', $value)) {
            $value = substr($value, 0, strpos($value, '.'));
        }

        return $value;
    }

    private static function columnIndex(string $reference): int
    {
        $letters = preg_replace('/\d/', '', $reference);
        $index = 0;
        foreach (str_split(strtoupper($letters)) as $char) {
            $index = $index * 26 + (ord($char) - 64);
        }

        return $index - 1;
    }

    /* ------------------------------------------------------------------ */

    /**
     * Buat file .xlsx. Baris pertama dicetak tebal (judul kolom),
     * semua kolom berformat teks agar NIM tidak berubah jadi angka.
     *
     * @param array<int, array<int, string>> $rows
     * @param array<int, int> $widths lebar kolom
     */
    public static function write(array $rows, array $widths = [], string $sheetName = 'Data'): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP "zip" belum aktif. Aktifkan extension=zip di php.ini.');
        }

        $esc = fn ($v) => htmlspecialchars((string) $v, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $cols = '';
        foreach ($widths as $i => $w) {
            $n = $i + 1;
            $cols .= '<col min="'.$n.'" max="'.$n.'" width="'.$w.'" style="2" customWidth="1"/>';
        }

        $sheetRows = '';
        foreach ($rows as $r => $row) {
            $cells = '';
            foreach ($row as $c => $value) {
                $ref = self::columnLetter($c).($r + 1);
                $style = $r === 0 ? 1 : 2;
                $cells .= '<c r="'.$ref.'" t="inlineStr" s="'.$style.'"><is><t xml:space="preserve">'.$esc($value).'</t></is></c>';
            }
            $sheetRows .= '<row r="'.($r + 1).'">'.$cells.'</row>';
        }

        $files = [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                .'<Default Extension="xml" ContentType="application/xml"/>'
                .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
                .'</Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                .'</Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="'.self::NS_REL.'">'
                .'<sheets><sheet name="'.$esc($sheetName).'" sheetId="1" r:id="rId1"/></sheets></workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
                .'</Relationships>',
            'xl/styles.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                .'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font></fonts>'
                .'<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill>'
                .'<fill><patternFill patternType="solid"><fgColor rgb="FF0A7C87"/><bgColor indexed="64"/></patternFill></fill></fills>'
                .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
                .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
                .'<cellXfs count="3">'
                .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
                .'<xf numFmtId="49" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyNumberFormat="1"/>'
                .'<xf numFmtId="49" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
                .'</cellXfs>'
                .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
                .'</styleSheet>',
            'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                .'<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
                .($cols ? '<cols>'.$cols.'</cols>' : '')
                .'<sheetData>'.$sheetRows.'</sheetData></worksheet>',
        ];

        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();
        $zip->open($tmp, ZipArchive::OVERWRITE);
        foreach ($files as $name => $content) {
            $zip->addFromString($name, $content);
        }
        $zip->close();

        $binary = file_get_contents($tmp);
        @unlink($tmp);

        return $binary;
    }

    private static function columnLetter(int $index): string
    {
        $letter = '';
        for ($i = $index + 1; $i > 0; $i = intdiv($i - 1, 26)) {
            $letter = chr(65 + ($i - 1) % 26).$letter;
        }

        return $letter;
    }
}
