<?php

namespace App\Http\Controllers\Kampus;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\PersonName;
use App\Rules\StartsWithLetter;
use App\Support\SimpleXlsx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

/**
 * Admin kampus mendaftarkan banyak mahasiswa sekaligus dari file Excel/CSV.
 * Alur: unggah file -> pratinjau (setiap baris dicek) -> konfirmasi -> akun dibuat & langsung terverifikasi.
 */
class StudentImportController extends Controller
{
    private const MAX_ROWS = 1000;
    private const SESSION_KEY = 'student_import';

    /** Nama judul kolom yang dikenali (huruf besar/kecil tidak berpengaruh) */
    private const HEADERS = [
        'name'     => ['nama', 'nama lengkap', 'name'],
        'nim'      => ['nim'],
        'prodi'    => ['prodi', 'program studi', 'jurusan'],
        'email'    => ['email', 'email kampus', 'e-mail'],
        'password' => ['kata sandi', 'kata_sandi', 'password'],
    ];

    /** Halaman impor (+ pratinjau jika file sudah diunggah) */
    public function create(Request $request)
    {
        $import = $request->session()->get(self::SESSION_KEY);

        // Pratinjau hanya berlaku untuk kampus yang sama
        if ($import && ($import['campus_id'] ?? null) !== $request->user()->campus_id) {
            $import = null;
        }

        return view('kampus.students.import', [
            'import'     => $import,
            'validCount' => $import ? collect($import['rows'])->where('valid', true)->count() : 0,
        ]);
    }

    /** Unduh template Excel */
    public function template(Request $request)
    {
        $domain = $request->user()->campus->email_domain ?: 'students.kampus.ac.id';

        $binary = SimpleXlsx::write([
            ['nama', 'nim', 'prodi', 'email', 'kata_sandi'],
            ['Budi Santoso', '221402001', 'Teknologi Informasi', 'budi.santoso@'.$domain, ''],
            ['Siti Aisyah', '221402002', 'Sistem Informasi', 'siti.aisyah@'.$domain, 'rahasia123'],
        ], [28, 16, 26, 36, 18], 'Mahasiswa');

        return response($binary, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template-impor-mahasiswa.xlsx"',
        ]);
    }

    /** Unggah file lalu cek setiap baris */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv,txt', 'max:2048'],
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes'    => 'File harus berformat .xlsx (Excel) atau .csv.',
            'file.max'      => 'Ukuran file maksimal 2 MB.',
        ]);

        $file = $request->file('file');

        try {
            $sheet = SimpleXlsx::read($file->getRealPath(), $file->getClientOriginalExtension());
        } catch (RuntimeException $e) {
            return back()->withErrors(['file' => $e->getMessage()]);
        }

        // Cari baris judul kolom
        $map = $this->mapHeaders($sheet[0] ?? []);
        $missing = array_diff(['name', 'nim', 'prodi', 'email'], array_keys($map));
        if ($missing) {
            $labels = ['name' => 'nama', 'nim' => 'nim', 'prodi' => 'prodi', 'email' => 'email'];

            return back()->withErrors(['file' => 'Kolom berikut tidak ditemukan di baris pertama: '
                .implode(', ', array_map(fn ($k) => $labels[$k], $missing)).'. Gunakan template yang disediakan.']);
        }

        // Ambil baris data (lewati baris kosong)
        $records = [];
        foreach (array_slice($sheet, 1, null, true) as $i => $row) {
            $data = [];
            foreach ($map as $key => $col) {
                $data[$key] = trim((string) ($row[$col] ?? ''));
            }
            if (implode('', $data) === '') {
                continue;
            }
            $data['email'] = mb_strtolower($data['email']);
            $records[] = ['line' => $i + 1, 'data' => $data];
        }

        if (! $records) {
            return back()->withErrors(['file' => 'File tidak berisi data mahasiswa.']);
        }
        if (count($records) > self::MAX_ROWS) {
            return back()->withErrors(['file' => 'Maksimal '.self::MAX_ROWS.' mahasiswa per file. Bagi file menjadi beberapa bagian.']);
        }

        $campusId = $request->user()->campus_id;
        $rows = $this->checkRows($records, $campusId);

        $request->session()->put(self::SESSION_KEY, [
            'campus_id' => $campusId,
            'file'      => $file->getClientOriginalName(),
            'rows'      => $rows,
        ]);

        return redirect()->route('kampus.students.import');
    }

    /** Daftarkan baris yang valid */
    public function store(Request $request)
    {
        $import = $request->session()->get(self::SESSION_KEY);
        $campusId = $request->user()->campus_id;

        if (! $import || $import['campus_id'] !== $campusId) {
            return redirect()->route('kampus.students.import')->withErrors(['file' => 'Unggah file terlebih dahulu.']);
        }

        // Cek ulang sebelum disimpan (data bisa berubah sejak pratinjau)
        $records = collect($import['rows'])->where('valid', true)
            ->map(fn ($r) => ['line' => $r['line'], 'data' => $r['data']])->values()->all();
        $rows = collect($this->checkRows($records, $campusId));
        $valid = $rows->where('valid', true);

        DB::transaction(function () use ($valid, $campusId) {
            foreach ($valid as $row) {
                $d = $row['data'];
                User::create([
                    'name'                  => $d['name'],
                    'nim'                   => $d['nim'],
                    'prodi'                 => $d['prodi'],
                    'email'                 => $d['email'],
                    'password'              => $d['password'] !== '' ? $d['password'] : $d['nim'],   // default: NIM
                    'role'                  => User::ROLE_MAHASISWA,
                    'campus_id'             => $campusId,
                    'email_verified_at'     => now(),        // didaftarkan kampus = langsung terverifikasi
                    'verified_by_campus_at' => now(),
                ]);
            }
        });

        $request->session()->forget(self::SESSION_KEY);

        $skipped = count($import['rows']) - $valid->count();
        $message = $valid->count().' mahasiswa berhasil didaftarkan dan langsung terverifikasi.';
        if ($skipped > 0) {
            $message .= ' '.$skipped.' baris dilewati karena datanya belum benar.';
        }

        return redirect()->route('kampus.students.index', ['filter' => 'sudah'])->with('success', $message);
    }

    /** Batalkan pratinjau */
    public function cancel(Request $request)
    {
        $request->session()->forget(self::SESSION_KEY);

        return redirect()->route('kampus.students.import');
    }

    /* ------------------------------------------------------------------ */

    private function mapHeaders(array $header): array
    {
        $map = [];
        foreach ($header as $col => $title) {
            $title = mb_strtolower(trim(str_replace(['*', ':'], '', (string) $title)));
            foreach (self::HEADERS as $key => $aliases) {
                if (! isset($map[$key]) && in_array($title, $aliases, true)) {
                    $map[$key] = $col;
                }
            }
        }

        return $map;
    }

    /** Cek setiap baris: format, ganda di file, dan sudah terdaftar di sistem */
    private function checkRows(array $records, int $campusId): array
    {
        $emails = array_column(array_column($records, 'data'), 'email');
        $nims = array_column(array_column($records, 'data'), 'nim');

        $usedEmails = User::whereIn('email', $emails)->pluck('email')->map(fn ($e) => mb_strtolower($e))->all();
        $usedNims = User::where('campus_id', $campusId)->where('role', User::ROLE_MAHASISWA)
            ->whereIn('nim', $nims)->pluck('nim')->all();

        $emailCount = array_count_values(array_filter($emails));
        $nimCount = array_count_values(array_filter($nims));

        $rules = [
            'name'     => ['required', 'string', 'max:255', new PersonName],
            'nim'      => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9.\-]+$/'],
            'prodi'    => ['required', 'string', 'max:255', new StartsWithLetter],
            'email'    => ['required', 'email', 'max:255', 'regex:/\.ac\.id$/i'],
            'password' => ['nullable', 'string', 'min:8', 'max:100'],
        ];
        $messages = [
            'email.regex' => 'Email harus berakhiran .ac.id.',
            'nim.regex'   => 'NIM hanya boleh berisi huruf, angka, titik, dan tanda hubung.',
        ];

        return array_map(function ($record) use ($rules, $messages, $usedEmails, $usedNims, $emailCount, $nimCount) {
            $d = $record['data'] + ['password' => ''];
            $errors = Validator::make($d, $rules, $messages)->errors()->all();

            if ($d['email'] !== '' && ($emailCount[$d['email']] ?? 0) > 1) {
                $errors[] = 'Email muncul lebih dari sekali di file.';
            }
            if ($d['nim'] !== '' && ($nimCount[$d['nim']] ?? 0) > 1) {
                $errors[] = 'NIM muncul lebih dari sekali di file.';
            }
            if (in_array($d['email'], $usedEmails, true)) {
                $errors[] = 'Email sudah terdaftar di CELS.';
            }
            if (in_array($d['nim'], $usedNims, true)) {
                $errors[] = 'NIM sudah terdaftar di kampus ini.';
            }

            return [
                'line'   => $record['line'],
                'data'   => $d,
                'errors' => $errors,
                'valid'  => ! $errors,
            ];
        }, $records);
    }
}
