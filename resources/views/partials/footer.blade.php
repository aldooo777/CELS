<footer class="cels-footer">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center mb-2">
                    <span class="fw-bold fs-3 text-white me-2">CELS</span>
                    <span class="badge bg-white py-1 px-2" style="font-size:.65rem;font-weight:700;color:#0A7C87!important;">SaaS</span>
                </div>
                <p class="small text-white-50 mb-3">{{ config('cels.longname') }}</p>
                <p class="small text-light" style="opacity:.85;">
                    Platform web SaaS yang membantu perguruan tinggi di Indonesia mengelola, memvalidasi, dan memublikasikan program Pembelajaran di Luar Kampus (PDLK) secara akuntabel dan efisien.
                </p>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h5>Tautan Cepat</h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="{{ route('home') }}#beranda">Beranda</a></li>
                    <li class="mb-2"><a href="{{ route('home') }}#fitur">Fitur</a></li>
                    <li class="mb-2"><a href="{{ route('home') }}#paket">Paket</a></li>
                    <li class="mb-2"><a href="{{ route('home') }}#faq">FAQ</a></li>
                    <li class="mb-2"><a href="{{ route('home') }}#kontak">Kontak</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-3 col-6">
                <h5>Kontak Singkat</h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><i class="bi bi-envelope me-2 text-warning"></i>{{ config('cels.contact.email') }}</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2 text-warning"></i>{{ config('cels.contact.whatsapp') }}</li>
                    <li class="mb-2"><i class="bi bi-clock me-2 text-warning"></i>{{ config('cels.contact.hours') }}</li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5>Sosial Media</h5>
                <div class="d-flex gap-3 fs-5 mb-3">
                    <a class="text-white-50" href="{{ config('cels.contact.instagram') }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-instagram"></i></a>
                    <a class="text-white-50" href="{{ config('cels.contact.linkedin') }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-linkedin"></i></a>
                </div>
                <p class="small text-white-50 mb-0">Terhubung dengan komunitas pengelola program PDLK di Indonesia.</p>
            </div>
        </div>
        <div class="footer-divider-orange"></div>
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center small text-white-50">
            <div>&copy; {{ date('Y') }} CELS - {{ config('cels.longname') }}. All rights reserved.</div>
            <div class="mt-2 mt-sm-0">
                <a class="text-white-50 me-3" href="{{ route('home') }}#kontak">Kebijakan Privasi</a>
                <a class="text-white-50" href="{{ route('home') }}#kontak">Syarat &amp; Ketentuan</a>
            </div>
        </div>
    </div>
</footer>
