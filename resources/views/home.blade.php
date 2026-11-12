@extends('layouts.app')
@section('title', 'Beranda')
@section('body-class', 'page-landing')
@section('full-footer', true)

@section('page')

{{-- 1. BERANDA (Hero) --}}
<section class="hero-section text-center" id="beranda">
    <div class="container hero-content">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                @if (session('success'))
                    <div class="alert alert-success text-start mx-auto" style="max-width:720px">{{ session('success') }}</div>
                @endif
                <span class="hero-tagline"><i class="bi bi-mortarboard-fill me-1"></i> {{ config('cels.tagline') }}</span>
                <h1 class="hero-title">Kelola pembelajaran di luar kampus dalam satu platform</h1>
                <p class="hero-subtitle">Dari publikasi program, pendaftaran mahasiswa, sampai persetujuan dan rekap.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-3">
                    <a class="btn btn-cels-cta btn-lg px-4 shadow-sm" href="{{ route('campus-registration.create') }}">Daftarkan kampus</a>
                    <a class="btn btn-cels-outline-light btn-lg px-4" href="#kontak">Minta demo</a>
                </div>
                <p class="small text-white-50 mb-1">
                    <i class="bi bi-info-circle me-1 text-warning"></i> Kampus baru dapat mencoba gratis selama {{ \App\Models\Campus::TRIAL_DAYS }} hari.
                </p>
                <p class="small text-white-50 mb-0">
                    Mahasiswa? <a href="{{ route('register') }}" class="text-white fw-semibold">Daftar gratis</a> dengan email kampus.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Kartu statistik di bawah hero --}}
<div class="container stat-cards-wrapper">
    <div class="row g-3">
        @foreach (config('cels.stats') as [$number, $label])
            <div class="col-6 col-md-3">
                <div class="landing-stat">
                    <div class="stat-number">{{ $number }}</div>
                    <div class="stat-label">{{ $label }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="text-center mt-2"><small class="text-muted fst-italic">{{ config('cels.stats_source') }}</small></div>
</div>

{{-- 2. MASALAH --}}
<section class="section-alt mt-4" id="masalah">
    <div class="container">
        <div class="section-heading">
            <span class="badge-cels-secondary mb-2 d-inline-block">Tantangan Operasional</span>
            <h2>Pengelolaan PDLK di banyak kampus masih terpecah</h2>
            <p>Proses administrasi yang tercecer menyita waktu staf kemahasiswaan dan memperlambat pelaporan akreditasi.</p>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach ([
                ['diagram-3', 'Data tersebar', 'Informasi program, kuota, dan arsip berada di sistem akademik, Google Form, Spreadsheet, dan file Excel yang terpisah tanpa sinkronisasi terpusat.'],
                ['clock-history', 'Rekap manual', 'Unit pengelola harus memvalidasi serta menggabungkan data dari puluhan formulir dan mitra magang setiap akhir semester secara berulang.'],
                ['file-earmark-excel', 'Persiapan akreditasi lama', 'Data pembuktian kegiatan luar kampus untuk borang LAM atau BAN-PT harus disusun ulang saat dibutuhkan, berisiko kehilangan rekam jejak.'],
            ] as [$icon, $title, $text])
                <div class="col-md-4">
                    <div class="card card-cels card-top-tosca p-4 h-100">
                        <div class="mb-3 fs-1" style="color: var(--cels-navbar);"><i class="bi bi-{{ $icon }}"></i></div>
                        <h4 class="h5 mb-3">{{ $title }}</h4>
                        <p class="mb-0 text-muted">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <small class="text-muted fst-italic">Sumber: wawancara dengan UPT PP-ESTEM Institut Teknologi Del, September 2026.</small>
        </div>
    </div>
</section>

{{-- 3. FITUR --}}
<section id="fitur">
    <div class="container">
        <div class="section-heading">
            <span class="badge-cels-primary mb-2 d-inline-block">Solusi Komprehensif</span>
            <h2>Informasi saja tidak cukup. CELS mengelola seluruh perjalanannya.</h2>
            <p>Dari pendaftaran kampus, publikasi program, hingga seleksi mahasiswa, semuanya tercatat dalam satu platform.</p>
        </div>
        <div class="row g-4">
            @foreach ([
                ['building-check', 'Registrasi kampus terverifikasi', 'Hanya perguruan tinggi yang lolos verifikasi tim CELS, dengan surat tugas dari unit berwenang, yang dapat mengaktifkan akun institusi.', false],
                ['megaphone', 'Publikasi kesempatan PDLK', 'Publikasikan magang, kerja praktik, KKN, riset, studi independen, dan pertukaran pelajar lengkap dengan kuota, deadline, dan syarat.', false],
                ['mortarboard', 'Pendaftaran mahasiswa gratis', 'Mahasiswa mendaftar dengan email kampus, melihat program kampusnya, dan mendaftar dengan profil yang terisi otomatis.', false],
                ['person-check', 'Seleksi dan status pendaftaran', 'Status Diajukan, Diproses, Disetujui, atau Ditolak terpantau jelas, dengan notifikasi email setiap kali status berubah.', false],
                ['geo-alt', 'Monitoring kegiatan', 'Logbook mahasiswa selama kegiatan berlangsung, evaluasi berkala, dan pelaporan kendala di lapangan.', true],
                ['graph-up-arrow', 'Dashboard dan rekap', 'Rekap data mahasiswa PDLK yang siap dipakai untuk pelaporan PDDikti dan penyusunan IKU perguruan tinggi.', true],
            ] as [$icon, $title, $text, $soon])
                <div class="col-md-6 col-lg-4">
                    <div class="card card-cels feature-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">
                                <div class="p-2 rounded bg-light me-3" style="color: var(--cels-navbar); font-size: 1.6rem;"><i class="bi bi-{{ $icon }}"></i></div>
                                <h5 class="mb-0 fs-6">{{ $title }}</h5>
                            </div>
                            @if ($soon)<span class="badge-cels-soon text-nowrap">Segera Hadir</span>@endif
                        </div>
                        <p class="small text-muted mb-0">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 4. CARA KERJA --}}
<section class="section-alt" id="cara-kerja">
    <div class="container">
        <div class="section-heading">
            <span class="badge-cels-secondary mb-2 d-inline-block">Implementasi Cepat</span>
            <h2>Mulai dalam empat langkah</h2>
            <p>Dirancang ramah bagi staf administrasi tanpa memerlukan server tambahan di kampus.</p>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach ([
                ['Daftarkan kampus', 'Pilih kampus dari daftar, isi data penanggung jawab, dan lampirkan surat tugas unit pengelola kemahasiswaan.'],
                ['Tunggu verifikasi', 'Tim CELS memeriksa data dan dokumen kampus. Setelah disetujui, kampus langsung masuk masa uji coba.'],
                ['Publikasikan kesempatan', 'Posting program lengkap dengan jenis kegiatan, kuota, deadline, syarat, dan keterangan mitra.'],
                ['Kelola pendaftar', 'Periksa berkas pendaftar, setujui atau tolak, dan pantau semuanya dari dashboard kampus.'],
            ] as $i => [$title, $text])
                <div class="col-12 col-md-3 step-item">
                    <div class="step-connector"></div>
                    <div class="step-circle">{{ $i + 1 }}</div>
                    <h5 class="fs-6 mb-2">{{ $title }}</h5>
                    <p class="small text-muted mb-0">{{ $text }}</p>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-3">
            <a class="btn btn-cels-cta px-4" href="{{ route('campus-registration.create') }}">Daftarkan kampus sekarang</a>
        </div>
    </div>
</section>

{{-- 5. PERBANDINGAN --}}
<section id="perbandingan">
    <div class="container">
        <div class="section-heading">
            <span class="badge-cels-primary mb-2 d-inline-block">Evaluasi Sistem</span>
            <h2>Dibandingkan dengan cara yang dipakai sekarang</h2>
            <p>Lihat efisiensi operasional saat kampus Anda beralih ke CELS dibandingkan solusi manual atau pengembangan mandiri.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="table-responsive">
                    <table class="table table-bordered table-comparison align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:25%">Aspek</th>
                                <th style="width:25%">Formulir &amp; Spreadsheet</th>
                                <th style="width:25%">Sistem Internal Kampus</th>
                                <th class="cels-head text-center" style="width:25%">
                                    <div class="text-warning small text-uppercase fw-bold">Platform SaaS</div>CELS
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ([
                                ['Data terpusat', ['x', 'Terpecah di banyak link drive & sheet'], ['dash', 'Hanya internal, sulit lintas institusi'], 'Terpusat & terstandarisasi'],
                                ['Pemantauan status', ['x', 'Tidak ada riwayat status otomatis'], ['check', 'Tergantung kapasitas server lokal'], 'Notifikasi & lini masa status'],
                                ['Penyesuaian alur kampus', ['dash', 'Sangat manual via revisi form'], ['x', 'Butuh tim IT & siklus coding lama'], 'Konfigurasi program mandiri'],
                                ['Biaya bagi kampus', ['dash', 'Gratis alat, mahal waktu kerja staf'], ['x', 'Biaya server & tim IT tinggi'], 'Langganan terjangkau & terprediksi'],
                            ] as [$aspect, $form, $internal, $cels])
                                <tr>
                                    <td class="fw-medium text-dark">{{ $aspect }}</td>
                                    @foreach ([$form, $internal] as [$mark, $text])
                                        <td class="text-muted">
                                            <i class="bi {{ ['x' => 'bi-x-circle text-danger', 'dash' => 'bi-dash-circle text-secondary', 'check' => 'bi-check-circle text-secondary'][$mark] }} me-2"></i>{{ $text }}
                                        </td>
                                    @endforeach
                                    <td class="col-cels text-center"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ $cels }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 6. PAKET --}}
<section class="section-alt" id="paket">
    <div class="container">
        <div class="section-heading">
            <span class="badge-cels-secondary mb-2 d-inline-block">Investasi Institusi</span>
            <h2>Paket untuk kampus</h2>
            <p>Mahasiswa menggunakan CELS gratis. Hanya kampus yang berlangganan sesuai skala kebutuhan.</p>
        </div>
        <div class="row g-4 justify-content-center align-items-stretch">
            <div class="col-md-6 col-lg-5">
                <div class="pricing-card">
                    <h4 class="h5 mb-1">Paket Dasar</h4>
                    <p class="small text-muted mb-4">Cocok untuk mulai menata program magang dan kerja praktik di tingkat fakultas / unit.</p>
                    <div class="my-3 py-3 border-top border-bottom">
                        <span class="text-muted small d-block">Biaya Berlangganan:</span>
                        <span class="fs-4 fw-bold text-dark">Hubungi kami</span>
                    </div>
                    <ul class="list-unstyled my-4 flex-grow-1">
                        @foreach (['Registrasi & verifikasi akun resmi kampus', 'Publikasi program & seleksi pendaftar', 'Akses gratis tak terbatas untuk seluruh mahasiswa'] as $item)
                            <li class="mb-3 d-flex align-items-center"><i class="bi bi-check2-circle text-success fs-5 me-2"></i><span>{{ $item }}</span></li>
                        @endforeach
                    </ul>
                    <a class="btn btn-outline-dark w-100 py-2 mt-auto fw-semibold" href="{{ route('home', ['paket' => 'dasar']) }}#kontak" style="border-color: var(--cels-secondary); color: var(--cels-secondary);">Hubungi kami</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-5">
                <div class="pricing-card featured">
                    <span class="pricing-badge">Direkomendasikan</span>
                    <h4 class="h5 mb-1">Paket Premium</h4>
                    <p class="small text-muted mb-4">Solusi terpadu untuk monitoring kegiatan, rekap data, dan pelaporan IKU.</p>
                    <div class="my-3 py-3 border-top border-bottom">
                        <span class="text-muted small d-block">Biaya Berlangganan:</span>
                        <span class="fs-4 fw-bold text-dark">Hubungi kami</span>
                    </div>
                    <ul class="list-unstyled my-4 flex-grow-1">
                        @foreach (['Seluruh fitur Paket Dasar', 'Monitoring kegiatan mahasiswa & logbook berkala', 'Dashboard rekap data siap lapor PDDikti'] as $item)
                            <li class="mb-3 d-flex align-items-center"><i class="bi bi-check2-circle text-success fs-5 me-2"></i><span>{{ $item }}</span></li>
                        @endforeach
                    </ul>
                    <a class="btn btn-cels-cta w-100 py-2 mt-auto" href="{{ route('home', ['paket' => 'premium']) }}#kontak">Hubungi kami</a>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <p class="small text-muted mb-0"><i class="bi bi-sliders me-1"></i> Catatan: Harga menyesuaikan jumlah mahasiswa PDLK dan paket yang dipilih.</p>
        </div>
    </div>
</section>

{{-- 7. KAMPUS MITRA & TESTIMONI --}}
<section id="mitra">
    <div class="container">
        <div class="section-heading">
            <span class="badge-cels-primary mb-2 d-inline-block">Kolaborasi Akademik</span>
            <h2>Dipercaya kampus</h2>
            <p>Mendukung transformasi digital program pembelajaran di luar kampus di berbagai perguruan tinggi.</p>
        </div>
        <div class="row g-3 justify-content-center mb-5">
            @foreach (['building', 'mortarboard', 'bank', 'award'] as $icon)
                <div class="col-6 col-md-3">
                    <div class="partner-logo-box">
                        <i class="bi bi-{{ $icon }} fs-3 d-block mb-1 text-secondary"></i>
                        <span>Logo kampus uji coba</span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row g-4 justify-content-center">
            @foreach ([['Pengelola Unit PDLK', 'Perguruan Tinggi Uji Coba'], ['Admin Kemahasiswaan', 'Perguruan Tinggi Uji Coba']] as [$name, $org])
                <div class="col-md-6 col-lg-5">
                    <div class="testimonial-card">
                        <div class="mb-3"><i class="bi bi-quote fs-1" style="color: var(--cels-navbar);"></i></div>
                        <p class="fst-italic text-muted mb-4">"Testimoni akan ditampilkan setelah kampus uji coba memberi izin publikasi hasil evaluasi operasional."</p>
                        <div class="d-flex align-items-center pt-3 border-top">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width:46px;height:46px;color:var(--cels-navbar);">
                                <i class="bi bi-person-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold">{{ $name }}</h6>
                                <small class="text-muted">{{ $org }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 8. KABAR --}}
<section class="section-alt" id="kabar">
    <div class="container">
        <div class="section-heading">
            <span class="badge-cels-secondary mb-2 d-inline-block">Pembaruan &amp; Kebijakan</span>
            <h2>Kabar &amp; Wawasan Kampus</h2>
            <p>Informasi seputar pembelajaran di luar kampus, panduan akreditasi, dan praktik baik tata kelola.</p>
        </div>
        <div class="row g-4">
            @foreach ([
                ['journal-check', 'Regulasi', '14 Februari 2026', 'Standar Rekapitulasi SKS PDLK Sesuai Panduan BAN-PT Terbaru', 'Pedoman pencatatan kegiatan magang dan riset mandiri agar siap untuk audit akreditasi program studi.'],
                ['lightning-charge', 'Tata Kelola', '28 Januari 2026', 'Efisiensi Verifikasi Berkas Mahasiswa Magang di Era Digital', 'Bagaimana unit kemahasiswaan memangkas waktu validasi berkas dari hitungan pekan menjadi harian.'],
                ['briefcase', 'Kemitraan', '10 Januari 2026', 'Memperluas Jaringan Industri untuk Kerja Praktik Mahasiswa', 'Strategi menjalin kemitraan dengan institusi yang menyediakan program magang terstruktur.'],
            ] as [$icon, $tag, $date, $title, $text])
                <div class="col-md-4">
                    <div class="news-card">
                        <div class="news-thumb"><i class="bi bi-{{ $icon }}"></i></div>
                        <div class="news-card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge-cels-primary">{{ $tag }}</span>
                                <small class="text-muted">{{ $date }}</small>
                            </div>
                            <h5 class="fs-6 fw-bold mb-2">{{ $title }}</h5>
                            <p class="small text-muted mb-4">{{ $text }}</p>
                            <div class="mt-auto"><span class="btn btn-cels-secondary btn-sm w-100 disabled" aria-disabled="true">Segera tersedia</span></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 9. FAQ --}}
<section id="faq">
    <div class="container">
        <div class="section-heading">
            <span class="badge-cels-primary mb-2 d-inline-block">Pertanyaan Umum</span>
            <h2>Pertanyaan yang Sering Diajukan</h2>
            <p>Hal-hal yang sering ditanyakan oleh pimpinan unit kemahasiswaan dan pengelola PDLK.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion accordion-cels" id="celsFaqAccordion">
                    @foreach ([
                        ['Siapa yang membayar layanan CELS?', 'Pelanggan berbayar CELS adalah institusi perguruan tinggi (universitas, institut, politeknik, atau sekolah tinggi). Mahasiswa menggunakan fitur pencarian dan pendaftaran program di CELS secara gratis.'],
                        ['Bagaimana kampus diverifikasi?', 'Admin kampus memilih kampusnya dari daftar, mengisi data penanggung jawab dengan email institusi (.ac.id), dan mengunggah surat tugas. Tim CELS memeriksa keabsahan data dan dokumen tersebut sebelum akun kampus diaktifkan.'],
                        ['Apakah mahasiswa dari kampus lain bisa mendaftar?', 'Pada tahap awal, mahasiswa melihat dan mendaftar program yang dipublikasikan oleh kampusnya sendiri. Pendaftaran lintas kampus, dengan pengaturan kebijakan pelamar di setiap program, sedang kami siapkan untuk tahap berikutnya.'],
                        ['Bagaimana jika kampus sudah punya sistem sendiri?', 'CELS dapat berjalan berdampingan dengan sistem akademik kampus. Jika sistem internal hanya mencatat nilai akademik, CELS melengkapi bagian publikasi program, penerimaan berkas, dan seleksi mahasiswa.'],
                    ] as $i => [$q, $a])
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading{{ $i }}">
                                <button class="accordion-button @if($i > 0) collapsed @endif" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faqCollapse{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $i }}">
                                    {{ $q }}
                                </button>
                            </h2>
                            <div id="faqCollapse{{ $i }}" class="accordion-collapse collapse @if($i === 0) show @endif" aria-labelledby="faqHeading{{ $i }}" data-bs-parent="#celsFaqAccordion">
                                <div class="accordion-body">{{ $a }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 10. KONTAK --}}
<section class="section-alt" id="kontak">
    <div class="container">
        <div class="section-heading">
            <span class="badge-cels-secondary mb-2 d-inline-block">Konsultasi &amp; Pendaftaran</span>
            <h2>Siap merapikan pengelolaan PDLK di kampus Anda?</h2>
            <p>Konsultasikan kebutuhan perguruan tinggi Anda atau jadwalkan sesi demo bersama tim kami.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-lg-7">
                <div class="contact-form-card">
                    <h4 class="h5 mb-4 text-dark"><i class="bi bi-pencil-square me-2" style="color: var(--cels-navbar);"></i> Formulir Minta Demo / Hubungi Kami</h4>

                    @if (session('demo_success'))
                        <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i> {{ session('demo_success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger small mb-3">
                            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('demo-requests.store') }}">@csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold" for="institution_name">Nama Institusi / Perguruan Tinggi <span class="text-danger">*</span></label>
                                <input class="form-control" id="institution_name" name="institution_name" value="{{ old('institution_name') }}" placeholder="Contoh: Universitas Indonesia" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold" for="contact_name">Nama Kontak / Pejabat Unit <span class="text-danger">*</span></label>
                                <input class="form-control" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" placeholder="Contoh: Dr. Budi Santoso" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold" for="email">Email Resmi Institusi <span class="text-danger">*</span></label>
                                <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@kampus.ac.id" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold" for="phone">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                                <input class="form-control" id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="081234567890" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold" for="student_estimate">Perkiraan Jumlah Mahasiswa PDLK</label>
                                <input class="form-control" id="student_estimate" name="student_estimate" value="{{ old('student_estimate') }}" placeholder="Contoh: 500 - 1.500 mahasiswa/semester">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold" for="package">Paket yang Diminati</label>
                                <select class="form-select" id="package" name="package">
                                    @foreach (\App\Models\DemoRequest::PACKAGES as $key => $label)
                                        <option value="{{ $key }}" @selected(old('package', request('paket', 'belum_tahu')) === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold" for="message">Pesan / Catatan Tambahan</label>
                                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Ceritakan alur PDLK yang saat ini berjalan atau modul yang ingin diprioritaskan...">{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button class="btn btn-cels-cta w-100 py-2 fs-6" type="submit">Kirim Permintaan Demo</button>
                                <small class="text-muted d-block text-center mt-2">Data institusi Anda hanya dipakai untuk menindaklanjuti permintaan ini.</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card card-cels p-4 mb-4">
                    <h5 class="h6 fw-bold mb-3 text-dark">Informasi Layanan CELS</h5>
                    @foreach ([
                        ['envelope-fill', 'Email Resmi', config('cels.contact.email'), 'mailto:'.config('cels.contact.email')],
                        ['whatsapp', 'Layanan WhatsApp Admin', config('cels.contact.whatsapp').' ('.config('cels.contact.hours').')', 'https://wa.me/'.preg_replace('/\D/', '', config('cels.contact.whatsapp'))],
                        ['geo-alt-fill', 'Kantor Operasional', config('cels.contact.address'), null],
                    ] as [$icon, $label, $value, $link])
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3 fs-5" style="color: var(--cels-navbar);"><i class="bi bi-{{ $icon }}"></i></div>
                            <div>
                                <strong class="d-block small text-dark">{{ $label }}</strong>
                                @if ($link)
                                    <a class="text-decoration-none text-muted small" href="{{ $link }}" target="_blank" rel="noopener">{{ $value }}</a>
                                @else
                                    <p class="small text-muted mb-0">{{ $value }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="border rounded overflow-hidden" style="background:#e2e8f0;height:180px;">
                        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center p-3 text-muted">
                            <i class="bi bi-map fs-2 mb-1" style="color: var(--cels-navbar);"></i>
                            <span class="small fw-semibold">Peta Lokasi Kantor Layanan CELS</span>
                            <span style="font-size:.75rem;">Jakarta Pusat, DKI Jakarta</span>
                        </div>
                    </div>
                </div>
                <div class="card card-cels p-4">
                    <h5 class="h6 fw-bold mb-2 text-dark"><i class="bi bi-mortarboard me-1" style="color: var(--cels-navbar);"></i> Anda mahasiswa?</h5>
                    <p class="small text-muted mb-3">Buat akun gratis dengan email kampus untuk melihat dan mendaftar program PDLK dari kampusmu.</p>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm">Daftar sebagai mahasiswa</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
