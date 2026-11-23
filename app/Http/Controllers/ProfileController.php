<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\PersonName;
use App\Rules\StartsWithLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

/** Profil Saya: lihat & ubah data akun, foto profil, dan kata sandi (semua peran) */
class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user()->load('campus'),
        ]);
    }

    /** Simpan data akun */
    public function update(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name'  => ['required', 'string', 'max:255', new PersonName],
            'phone' => ['nullable', 'regex:/^(\+62|62|0)8[0-9]{8,11}$/'],
        ];
        if ($user->isMahasiswa()) {
            $rules['prodi'] = ['required', 'string', 'max:255', new StartsWithLetter];
        }
        if ($user->isAdminKampus()) {
            $rules['position'] = ['required', 'string', 'max:255', new StartsWithLetter];
        }

        $data = $request->validate($rules, [
            'phone.regex' => 'Nomor telepon harus nomor HP Indonesia, contoh: 081234567890.',
        ]);

        // Samakan format nomor: +628... / 628... menjadi 08...
        if (! empty($data['phone'])) {
            $data['phone'] = preg_replace('/^(\+62|62)/', '0', $data['phone']);
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Data akun berhasil diperbarui.');
    }

    /** Unggah / ganti foto profil */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'avatar.required' => 'Pilih foto terlebih dahulu.',
            'avatar.image'    => 'File harus berupa gambar.',
            'avatar.mimes'    => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'avatar.max'      => 'Ukuran foto maksimal 2 MB.',
        ]);

        $user = $request->user();
        if ($user->avatar_path) {
            Storage::delete($user->avatar_path);
        }

        $user->update(['avatar_path' => $request->file('avatar')->store('avatars')]);

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil diperbarui.');
    }

    /** Hapus foto profil (kembali ke inisial) */
    public function destroyPhoto(Request $request)
    {
        $user = $request->user();
        if ($user->avatar_path) {
            Storage::delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return redirect()->route('profile.edit')->with('success', 'Foto profil dihapus.');
    }

    /** Ganti kata sandi */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8), 'different:current_password'],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini salah.',
            'password.different'                 => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
        ]);

        $request->user()->update(['password' => $request->input('password')]);

        return redirect()->route('profile.edit')->with('success', 'Kata sandi berhasil diganti.');
    }

    /** Tampilkan foto profil (disimpan privat) */
    public function photo(User $user)
    {
        abort_unless($user->avatar_path && Storage::exists($user->avatar_path), 404);

        return Storage::response($user->avatar_path, null, ['Cache-Control' => 'private, max-age=86400']);
    }
}
