<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\StudentRegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CampusRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Kampus;
use App\Http\Controllers\Mahasiswa;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GUEST (pengunjung tanpa akun)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/kontak', [DemoRequestController::class, 'store'])->middleware('throttle:5,1')->name('demo-requests.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    // Registrasi mahasiswa (gratis)
    Route::get('/daftar', [StudentRegisterController::class, 'create'])->name('register');
    Route::post('/daftar', [StudentRegisterController::class, 'store']);

    // Daftarkan Kampus (pintu penjualan)
    Route::get('/daftarkan-kampus', [CampusRegistrationController::class, 'create'])->name('campus-registration.create');
    Route::post('/daftarkan-kampus', [CampusRegistrationController::class, 'store'])->name('campus-registration.store');
});

/*
|--------------------------------------------------------------------------
| SEMUA PENGGUNA LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Verifikasi email (nama route mengikuti bawaan Laravel)
    Route::get('/email/verifikasi', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verifikasi/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/email/verifikasi/kirim-ulang', [VerificationController::class, 'send'])
        ->middleware('throttle:6,1')->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| ADMIN CELS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin_cels'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/verifikasi-kampus', [Admin\CampusRegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/verifikasi-kampus/{registration}', [Admin\CampusRegistrationController::class, 'show'])->name('registrations.show');
    Route::get('/verifikasi-kampus/{registration}/dokumen', [Admin\CampusRegistrationController::class, 'document'])->name('registrations.document');
    Route::post('/verifikasi-kampus/{registration}/setujui', [Admin\CampusRegistrationController::class, 'approve'])->name('registrations.approve');
    Route::post('/verifikasi-kampus/{registration}/tolak', [Admin\CampusRegistrationController::class, 'reject'])->name('registrations.reject');

    Route::get('/permintaan-demo', [Admin\DemoRequestController::class, 'index'])->name('demo-requests.index');
    Route::patch('/permintaan-demo/{demoRequest}', [Admin\DemoRequestController::class, 'toggle'])->name('demo-requests.toggle');

    Route::get('/kampus', [Admin\CampusController::class, 'index'])->name('campuses.index');
    Route::patch('/kampus/{campus}/langganan', [Admin\CampusController::class, 'updateSubscription'])->name('campuses.subscription');
});

/*
|--------------------------------------------------------------------------
| ADMIN KAMPUS (kemahasiswaan / unit PDLK)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin_kampus'])->prefix('kampus')->name('kampus.')->group(function () {
    // Halaman status saat pendaftaran kampus masih menunggu / ditolak
    Route::get('/status', [Kampus\DashboardController::class, 'status'])->name('status');

    Route::middleware('campus.active')->group(function () {
        Route::get('/', [Kampus\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('program', Kampus\ProgramController::class)
            ->parameters(['program' => 'program'])->names('programs');

        Route::get('/pendaftar/{application}', [Kampus\ApplicationController::class, 'show'])->name('applications.show');
        Route::patch('/pendaftar/{application}/status', [Kampus\ApplicationController::class, 'updateStatus'])->name('applications.status');
        Route::get('/pendaftar/{application}/berkas/{type}', [Kampus\ApplicationController::class, 'download'])
            ->whereIn('type', ['cv', 'dokumen'])->name('applications.download');

        Route::get('/mahasiswa', [Kampus\StudentController::class, 'index'])->name('students.index');
        Route::post('/mahasiswa/{student}/verifikasi', [Kampus\StudentController::class, 'verify'])->name('students.verify');
    });
});

/*
|--------------------------------------------------------------------------
| MAHASISWA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:mahasiswa', 'verified'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/', [Mahasiswa\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/program', [Mahasiswa\ProgramController::class, 'index'])->name('programs.index');
    Route::get('/program/{program}', [Mahasiswa\ProgramController::class, 'show'])->name('programs.show');

    Route::get('/program/{program}/daftar', [Mahasiswa\ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/program/{program}/daftar', [Mahasiswa\ApplicationController::class, 'store'])->name('applications.store');

    Route::get('/pendaftaran', [Mahasiswa\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/pendaftaran/{application}', [Mahasiswa\ApplicationController::class, 'show'])->name('applications.show');
});
