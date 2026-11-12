<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\CampusRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Guest klik "Daftarkan Kampus" */
class CampusRegistrationController extends Controller
{
    public function create()
    {
        return view('campus-registration.create', [
            // Hanya kampus yang belum terdaftar / pernah ditolak yang bisa dipilih
            'campuses' => Campus::where('is_listed', true)
                ->whereIn('status', ['belum_terdaftar', 'nonaktif'])
                ->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'campus_id'         => ['required'],   // id kampus atau "lainnya"
            'new_campus_name'   => ['required_if:campus_id,lainnya', 'nullable', 'string', 'max:255'],
            'new_campus_city'   => ['required_if:campus_id,lainnya', 'nullable', 'string', 'max:100'],
            'pic_name'          => ['required', 'string', 'max:255'],
            'pic_position'      => ['required', 'string', 'max:255'],
            'pic_phone'         => ['required', 'string', 'max:30'],
            'institution_email' => ['required', 'email', 'max:255', 'unique:users,email', 'regex:/\.ac\.id$/i'],
            'document'          => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'password'          => ['required', 'confirmed', Password::min(8)],
        ], [
            'institution_email.regex'   => 'Gunakan email institusi yang berakhiran .ac.id.',
            'new_campus_name.required_if' => 'Isi nama kampus jika kampus tidak ada di daftar.',
            'new_campus_city.required_if' => 'Isi kota kampus jika kampus tidak ada di daftar.',
        ]);

        if ($data['campus_id'] !== 'lainnya') {
            $request->validate([
                'campus_id' => [Rule::exists('campuses', 'id')->whereIn('status', ['belum_terdaftar', 'nonaktif'])],
            ], ['campus_id.exists' => 'Kampus ini sudah terdaftar atau sedang diverifikasi.']);
        }

        $user = DB::transaction(function () use ($request, $data) {
            // 1. Tentukan kampus (dari daftar PTN atau kampus baru)
            if ($data['campus_id'] === 'lainnya') {
                $campus = Campus::create([
                    'name'      => $data['new_campus_name'],
                    'city'      => $data['new_campus_city'],
                    'type'      => 'Lainnya',
                    'is_listed' => false,
                    'status'    => 'menunggu',
                ]);
            } else {
                $campus = Campus::findOrFail($data['campus_id']);
                $campus->update(['status' => 'menunggu']);
            }

            // 2. Buat akun admin kampus (belum aktif sampai disetujui)
            $user = User::create([
                'name'      => $data['pic_name'],
                'email'     => $data['institution_email'],
                'password'  => $data['password'],
                'role'      => User::ROLE_ADMIN_KAMPUS,
                'campus_id' => $campus->id,
                'phone'     => $data['pic_phone'],
                'position'  => $data['pic_position'],
            ]);

            // 3. Simpan pengajuan + dokumen pendukung (disimpan privat)
            CampusRegistration::create([
                'campus_id'         => $campus->id,
                'user_id'           => $user->id,
                'pic_name'          => $data['pic_name'],
                'pic_position'      => $data['pic_position'],
                'pic_phone'         => $data['pic_phone'],
                'institution_email' => $data['institution_email'],
                'document_path'     => $request->file('document')->store('campus-documents'),
                'status'            => 'menunggu',
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('kampus.status')
            ->with('success', 'Pendaftaran kampus terkirim. Status akun: Menunggu verifikasi.');
    }
}
