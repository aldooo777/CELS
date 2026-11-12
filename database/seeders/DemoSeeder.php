<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\CampusRegistration;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Data contoh untuk demo / pengujian.
 * Semua akun memakai kata sandi: password
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 0. File contoh surat tugas agar tombol "Lihat surat tugas" bisa dicoba
        Storage::put('campus-documents/contoh-surat-tugas.pdf', $this->samplePdf());

        // 1. Admin CELS
        User::updateOrCreate(['email' => 'admin@cels.test'], [
            'name' => 'Admin CELS', 'password' => 'password',
            'role' => User::ROLE_ADMIN_CELS, 'email_verified_at' => now(),
        ]);

        // 2. Kampus yang sudah disetujui (masa uji coba)
        $usu = Campus::where('name', 'Universitas Sumatera Utara')->first();
        $usu->update(['status' => 'uji_coba', 'trial_ends_at' => now()->addDays(Campus::TRIAL_DAYS), 'email_domain' => 'usu.ac.id']);

        $adminUsu = User::updateOrCreate(['email' => 'pdlk@usu.ac.id'], [
            'name' => 'Rina Siregar', 'password' => 'password', 'role' => User::ROLE_ADMIN_KAMPUS,
            'campus_id' => $usu->id, 'position' => 'Kepala Unit PDLK', 'phone' => '081200000001',
            'email_verified_at' => now(),
        ]);

        CampusRegistration::updateOrCreate(['user_id' => $adminUsu->id], [
            'campus_id' => $usu->id, 'pic_name' => $adminUsu->name, 'pic_position' => $adminUsu->position,
            'pic_phone' => $adminUsu->phone, 'institution_email' => $adminUsu->email,
            'document_path' => 'campus-documents/contoh-surat-tugas.pdf', 'status' => 'disetujui',
            'reviewed_at' => now(),
        ]);

        // 3. Kampus yang masih menunggu verifikasi
        $unand = Campus::where('name', 'Universitas Andalas')->first();
        $unand->update(['status' => 'menunggu']);

        $adminUnand = User::updateOrCreate(['email' => 'kemahasiswaan@unand.ac.id'], [
            'name' => 'Budi Santoso', 'password' => 'password', 'role' => User::ROLE_ADMIN_KAMPUS,
            'campus_id' => $unand->id, 'position' => 'Staf Kemahasiswaan', 'phone' => '081200000002',
        ]);

        CampusRegistration::updateOrCreate(['user_id' => $adminUnand->id], [
            'campus_id' => $unand->id, 'pic_name' => $adminUnand->name, 'pic_position' => $adminUnand->position,
            'pic_phone' => $adminUnand->phone, 'institution_email' => $adminUnand->email,
            'document_path' => 'campus-documents/contoh-surat-tugas.pdf', 'status' => 'menunggu',
        ]);

        // 4. Mahasiswa USU (sudah terverifikasi) + satu yang belum
        User::updateOrCreate(['email' => 'mahasiswa@students.usu.ac.id'], [
            'name' => 'Andi Pratama', 'password' => 'password', 'role' => User::ROLE_MAHASISWA,
            'campus_id' => $usu->id, 'nim' => '221402001', 'prodi' => 'Teknologi Informasi',
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'sari@students.usu.ac.id'], [
            'name' => 'Sari Lubis', 'password' => 'password', 'role' => User::ROLE_MAHASISWA,
            'campus_id' => $usu->id, 'nim' => '221402002', 'prodi' => 'Ilmu Komputer',
        ]);

        // 5. Contoh program
        $programs = [
            ['Magang Software Engineer', 'magang', 10, 'Program magang 4 bulan di bidang pengembangan perangkat lunak.', "Mahasiswa aktif minimal semester 5\nMenguasai dasar pemrograman web\nIPK minimal 3.00", 'Mitra: PT Teknologi Nusantara (www.contoh-mitra.co.id)'],
            ['Kerja Praktik Jaringan Komputer', 'kerja_praktik', 5, 'Kerja praktik di unit infrastruktur jaringan selama 2 bulan.', "Mahasiswa aktif minimal semester 4\nMemahami dasar jaringan komputer", 'Mitra: Dinas Kominfo Kota Medan'],
            ['Asisten Riset Kecerdasan Buatan', 'riset', 3, 'Membantu riset dosen di bidang machine learning.', "Mahasiswa aktif minimal semester 5\nMenguasai Python", null],
        ];

        foreach ($programs as [$title, $type, $quota, $desc, $req, $partner]) {
            Program::updateOrCreate(['campus_id' => $usu->id, 'title' => $title], [
                'created_by' => $adminUsu->id, 'type' => $type, 'quota' => $quota,
                'description' => $desc, 'requirements' => $req, 'partner_info' => $partner,
                'deadline' => now()->addWeeks(3), 'is_open' => true,
            ]);
        }
    }

    /** PDF satu halaman sederhana berisi teks "Contoh Surat Tugas" */
    private function samplePdf(): string
    {
        $text = 'BT /F1 20 Tf 72 720 Td (Contoh Surat Tugas - CELS) Tj ET';
        $objs = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            "<< /Length ".strlen($text)." >>\nstream\n{$text}\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objs as $i => $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1)." 0 obj\n{$obj}\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objs) + 1)."\n0000000000 65535 f \n";
        foreach ($offsets as $o) {
            $pdf .= sprintf("%010d 00000 n \n", $o);
        }
        return $pdf."trailer\n<< /Size ".(count($objs) + 1)." /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";
    }
}
