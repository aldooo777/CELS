-- =====================================================================
-- CELS - Skema database PostgreSQL + data awal
-- Jalankan di pgAdmin: klik database "cels" -> Tools -> Query Tool
-- PERINGATAN: bagian 0 MENGHAPUS semua tabel CELS beserta isinya.
-- =====================================================================

BEGIN;

SET TIME ZONE 'Asia/Jakarta';

-- ---------------------------------------------------------------------
-- 0. Hapus tabel lama (jika ada)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS applications, programs, campus_registrations, demo_requests,
    sessions, password_reset_tokens, users, campuses,
    cache, cache_locks, jobs, job_batches, failed_jobs, migrations CASCADE;

-- ---------------------------------------------------------------------
-- 1. Tabel bawaan Laravel (sesi, cache, antrean email)
-- ---------------------------------------------------------------------
CREATE TABLE cache (
    key        VARCHAR(255) PRIMARY KEY,
    value      TEXT    NOT NULL,
    expiration INTEGER NOT NULL
);
CREATE INDEX cache_expiration_index ON cache (expiration);

CREATE TABLE cache_locks (
    key        VARCHAR(255) PRIMARY KEY,
    owner      VARCHAR(255) NOT NULL,
    expiration INTEGER      NOT NULL
);
CREATE INDEX cache_locks_expiration_index ON cache_locks (expiration);

CREATE TABLE jobs (
    id           BIGSERIAL PRIMARY KEY,
    queue        VARCHAR(255) NOT NULL,
    payload      TEXT         NOT NULL,
    attempts     SMALLINT     NOT NULL,
    reserved_at  INTEGER,
    available_at INTEGER      NOT NULL,
    created_at   INTEGER      NOT NULL
);
CREATE INDEX jobs_queue_index ON jobs (queue);

CREATE TABLE job_batches (
    id             VARCHAR(255) PRIMARY KEY,
    name           VARCHAR(255) NOT NULL,
    total_jobs     INTEGER      NOT NULL,
    pending_jobs   INTEGER      NOT NULL,
    failed_jobs    INTEGER      NOT NULL,
    failed_job_ids TEXT         NOT NULL,
    options        TEXT,
    cancelled_at   INTEGER,
    created_at     INTEGER      NOT NULL,
    finished_at    INTEGER
);

CREATE TABLE failed_jobs (
    id         BIGSERIAL PRIMARY KEY,
    uuid       VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT         NOT NULL,
    queue      TEXT         NOT NULL,
    payload    TEXT         NOT NULL,
    exception  TEXT         NOT NULL,
    failed_at  TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- 2. Kampus (daftar PTN/PTS + status langganan)
-- ---------------------------------------------------------------------
CREATE TABLE campuses (
    id                   BIGSERIAL PRIMARY KEY,
    name                 VARCHAR(255) NOT NULL,
    short_name           VARCHAR(255),
    type                 VARCHAR(255) NOT NULL DEFAULT 'PTN',              -- PTN | PTS
    city                 VARCHAR(255),
    province             VARCHAR(255),
    email_domain         VARCHAR(255),
    is_listed            BOOLEAN      NOT NULL DEFAULT TRUE,               -- FALSE = "kampus tidak ada di daftar"
    status               VARCHAR(255) NOT NULL DEFAULT 'belum_terdaftar',  -- belum_terdaftar | menunggu | uji_coba | berlangganan | nonaktif
    trial_ends_at        DATE,
    subscription_ends_at DATE,
    created_at           TIMESTAMP(0),
    updated_at           TIMESTAMP(0)
);

-- ---------------------------------------------------------------------
-- 3. Pengguna (Admin CELS, Admin Kampus, Mahasiswa)
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id                    BIGSERIAL PRIMARY KEY,
    name                  VARCHAR(255) NOT NULL,
    email                 VARCHAR(255) NOT NULL UNIQUE,
    role                  VARCHAR(255) NOT NULL DEFAULT 'mahasiswa',       -- admin_cels | admin_kampus | mahasiswa
    campus_id             BIGINT REFERENCES campuses (id) ON DELETE SET NULL,
    nim                   VARCHAR(255),
    prodi                 VARCHAR(255),
    phone                 VARCHAR(255),
    position              VARCHAR(255),                                    -- jabatan PIC admin kampus
    email_verified_at     TIMESTAMP(0),
    verified_by_campus_at TIMESTAMP(0),
    password              VARCHAR(255) NOT NULL,
    remember_token        VARCHAR(100),
    created_at            TIMESTAMP(0),
    updated_at            TIMESTAMP(0)
);

CREATE TABLE password_reset_tokens (
    email      VARCHAR(255) PRIMARY KEY,
    token      VARCHAR(255) NOT NULL,
    created_at TIMESTAMP(0)
);

CREATE TABLE sessions (
    id            VARCHAR(255) PRIMARY KEY,
    user_id       BIGINT,
    ip_address    VARCHAR(45),
    user_agent    TEXT,
    payload       TEXT    NOT NULL,
    last_activity INTEGER NOT NULL
);
CREATE INDEX sessions_user_id_index ON sessions (user_id);
CREATE INDEX sessions_last_activity_index ON sessions (last_activity);

-- ---------------------------------------------------------------------
-- 4. Pengajuan pendaftaran kampus
-- ---------------------------------------------------------------------
CREATE TABLE campus_registrations (
    id                BIGSERIAL PRIMARY KEY,
    campus_id         BIGINT NOT NULL REFERENCES campuses (id) ON DELETE CASCADE,
    user_id           BIGINT NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    pic_name          VARCHAR(255) NOT NULL,
    pic_position      VARCHAR(255) NOT NULL,
    pic_phone         VARCHAR(255) NOT NULL,
    institution_email VARCHAR(255) NOT NULL,
    document_path     VARCHAR(255) NOT NULL,                               -- surat tugas
    status            VARCHAR(255) NOT NULL DEFAULT 'menunggu',            -- menunggu | disetujui | ditolak
    admin_note        TEXT,
    reviewed_by       BIGINT REFERENCES users (id) ON DELETE SET NULL,
    reviewed_at       TIMESTAMP(0),
    created_at        TIMESTAMP(0),
    updated_at        TIMESTAMP(0)
);

-- ---------------------------------------------------------------------
-- 5. Program PDLK
-- ---------------------------------------------------------------------
CREATE TABLE programs (
    id           BIGSERIAL PRIMARY KEY,
    campus_id    BIGINT NOT NULL REFERENCES campuses (id) ON DELETE CASCADE,
    created_by   BIGINT REFERENCES users (id) ON DELETE SET NULL,
    title        VARCHAR(255) NOT NULL,
    type         VARCHAR(255) NOT NULL,             -- magang | kerja_praktik | riset | pertukaran_pelajar | kkn | studi_independen | lainnya
    description  TEXT         NOT NULL,
    quota        INTEGER      NOT NULL CHECK (quota >= 0),
    deadline     DATE         NOT NULL,
    requirements TEXT         NOT NULL,
    partner_info TEXT,                              -- tautan / keterangan mitra
    is_open      BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at   TIMESTAMP(0),
    updated_at   TIMESTAMP(0)
);

-- ---------------------------------------------------------------------
-- 6. Pendaftaran mahasiswa ke program
-- ---------------------------------------------------------------------
CREATE TABLE applications (
    id                BIGSERIAL PRIMARY KEY,
    program_id        BIGINT NOT NULL REFERENCES programs (id) ON DELETE CASCADE,
    user_id           BIGINT NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    motivation        TEXT,
    cv_path           VARCHAR(255) NOT NULL,
    document_path     VARCHAR(255),
    status            VARCHAR(255) NOT NULL DEFAULT 'diajukan',          -- diajukan | diproses | disetujui | ditolak
    admin_note        TEXT,
    status_changed_at TIMESTAMP(0),
    created_at        TIMESTAMP(0),
    updated_at        TIMESTAMP(0),
    CONSTRAINT applications_program_id_user_id_unique UNIQUE (program_id, user_id)
);

-- ---------------------------------------------------------------------
-- 7. Permintaan demo dari landing page
-- ---------------------------------------------------------------------
CREATE TABLE demo_requests (
    id               BIGSERIAL PRIMARY KEY,
    institution_name VARCHAR(255) NOT NULL,
    contact_name     VARCHAR(255) NOT NULL,
    email            VARCHAR(255) NOT NULL,
    phone            VARCHAR(255) NOT NULL,
    student_estimate VARCHAR(255),
    package          VARCHAR(255) NOT NULL DEFAULT 'belum_tahu',           -- belum_tahu | dasar | premium
    message          TEXT,
    is_handled       BOOLEAN      NOT NULL DEFAULT FALSE,
    created_at       TIMESTAMP(0),
    updated_at       TIMESTAMP(0)
);

-- ---------------------------------------------------------------------
-- 8. Catatan migrasi Laravel
--    Supaya "php artisan migrate" tahu tabel-tabel ini sudah dibuat
--    dan tidak mencoba membuatnya lagi.
-- ---------------------------------------------------------------------
CREATE TABLE migrations (
    id        SERIAL PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch     INTEGER      NOT NULL
);
INSERT INTO migrations (migration, batch) VALUES
    ('0001_01_01_000000_create_users_table', 1),
    ('0001_01_01_000001_create_cache_table', 1),
    ('0001_01_01_000002_create_jobs_table', 1),
    ('2026_01_01_000001_create_campuses_table', 1),
    ('2026_01_01_000002_add_cels_fields_to_users_table', 1),
    ('2026_01_01_000003_create_campus_registrations_table', 1),
    ('2026_01_01_000004_create_programs_table', 1),
    ('2026_01_01_000005_create_applications_table', 1),
    ('2026_01_01_000006_create_demo_requests_table', 1);

-- =====================================================================
-- DATA AWAL
-- =====================================================================

-- 9. Daftar PTN
INSERT INTO campuses (name, short_name, city, province, type) VALUES
  ('Universitas Indonesia', 'UI', 'Depok', 'Jawa Barat', 'PTN'),
  ('Universitas Gadjah Mada', 'UGM', 'Sleman', 'DI Yogyakarta', 'PTN'),
  ('Institut Teknologi Bandung', 'ITB', 'Bandung', 'Jawa Barat', 'PTN'),
  ('IPB University', 'IPB', 'Bogor', 'Jawa Barat', 'PTN'),
  ('Institut Teknologi Sepuluh Nopember', 'ITS', 'Surabaya', 'Jawa Timur', 'PTN'),
  ('Universitas Airlangga', 'UNAIR', 'Surabaya', 'Jawa Timur', 'PTN'),
  ('Universitas Padjadjaran', 'UNPAD', 'Sumedang', 'Jawa Barat', 'PTN'),
  ('Universitas Diponegoro', 'UNDIP', 'Semarang', 'Jawa Tengah', 'PTN'),
  ('Universitas Brawijaya', 'UB', 'Malang', 'Jawa Timur', 'PTN'),
  ('Universitas Hasanuddin', 'UNHAS', 'Makassar', 'Sulawesi Selatan', 'PTN'),
  ('Universitas Sumatera Utara', 'USU', 'Medan', 'Sumatera Utara', 'PTN'),
  ('Universitas Andalas', 'UNAND', 'Padang', 'Sumatera Barat', 'PTN'),
  ('Universitas Sebelas Maret', 'UNS', 'Surakarta', 'Jawa Tengah', 'PTN'),
  ('Universitas Negeri Yogyakarta', 'UNY', 'Sleman', 'DI Yogyakarta', 'PTN'),
  ('Universitas Pendidikan Indonesia', 'UPI', 'Bandung', 'Jawa Barat', 'PTN'),
  ('Universitas Negeri Malang', 'UM', 'Malang', 'Jawa Timur', 'PTN'),
  ('Universitas Negeri Semarang', 'UNNES', 'Semarang', 'Jawa Tengah', 'PTN'),
  ('Universitas Negeri Surabaya', 'UNESA', 'Surabaya', 'Jawa Timur', 'PTN'),
  ('Universitas Negeri Jakarta', 'UNJ', 'Jakarta Timur', 'DKI Jakarta', 'PTN'),
  ('Universitas Negeri Padang', 'UNP', 'Padang', 'Sumatera Barat', 'PTN'),
  ('Universitas Negeri Medan', 'UNIMED', 'Medan', 'Sumatera Utara', 'PTN'),
  ('Universitas Negeri Makassar', 'UNM', 'Makassar', 'Sulawesi Selatan', 'PTN'),
  ('Universitas Negeri Manado', 'UNIMA', 'Minahasa', 'Sulawesi Utara', 'PTN'),
  ('Universitas Negeri Gorontalo', 'UNG', 'Gorontalo', 'Gorontalo', 'PTN'),
  ('Universitas Pendidikan Ganesha', 'UNDIKSHA', 'Singaraja', 'Bali', 'PTN'),
  ('Universitas Jember', 'UNEJ', 'Jember', 'Jawa Timur', 'PTN'),
  ('Universitas Jenderal Soedirman', 'UNSOED', 'Purwokerto', 'Jawa Tengah', 'PTN'),
  ('Universitas Lampung', 'UNILA', 'Bandar Lampung', 'Lampung', 'PTN'),
  ('Universitas Sriwijaya', 'UNSRI', 'Ogan Ilir', 'Sumatera Selatan', 'PTN'),
  ('Universitas Riau', 'UNRI', 'Pekanbaru', 'Riau', 'PTN'),
  ('Universitas Jambi', 'UNJA', 'Jambi', 'Jambi', 'PTN'),
  ('Universitas Bengkulu', 'UNIB', 'Bengkulu', 'Bengkulu', 'PTN'),
  ('Universitas Syiah Kuala', 'USK', 'Banda Aceh', 'Aceh', 'PTN'),
  ('Universitas Malikussaleh', 'UNIMAL', 'Lhokseumawe', 'Aceh', 'PTN'),
  ('Universitas Teuku Umar', 'UTU', 'Meulaboh', 'Aceh', 'PTN'),
  ('Universitas Samudra', 'UNSAM', 'Langsa', 'Aceh', 'PTN'),
  ('Universitas Maritim Raja Ali Haji', 'UMRAH', 'Tanjungpinang', 'Kepulauan Riau', 'PTN'),
  ('Universitas Bangka Belitung', 'UBB', 'Bangka', 'Kepulauan Bangka Belitung', 'PTN'),
  ('Universitas Sultan Ageng Tirtayasa', 'UNTIRTA', 'Serang', 'Banten', 'PTN'),
  ('Universitas Siliwangi', 'UNSIL', 'Tasikmalaya', 'Jawa Barat', 'PTN'),
  ('Universitas Trunojoyo Madura', 'UTM', 'Bangkalan', 'Jawa Timur', 'PTN'),
  ('Universitas Tidar', 'UNTIDAR', 'Magelang', 'Jawa Tengah', 'PTN'),
  ('Universitas Terbuka', 'UT', 'Tangerang Selatan', 'Banten', 'PTN'),
  ('Universitas Pembangunan Nasional Veteran Jakarta', 'UPNVJ', 'Jakarta Selatan', 'DKI Jakarta', 'PTN'),
  ('Universitas Pembangunan Nasional Veteran Yogyakarta', 'UPNVY', 'Sleman', 'DI Yogyakarta', 'PTN'),
  ('Universitas Pembangunan Nasional Veteran Jawa Timur', 'UPNVJT', 'Surabaya', 'Jawa Timur', 'PTN'),
  ('Universitas Udayana', 'UNUD', 'Badung', 'Bali', 'PTN'),
  ('Universitas Mataram', 'UNRAM', 'Mataram', 'Nusa Tenggara Barat', 'PTN'),
  ('Universitas Nusa Cendana', 'UNDANA', 'Kupang', 'Nusa Tenggara Timur', 'PTN'),
  ('Universitas Timor', 'UNIMOR', 'Kefamenanu', 'Nusa Tenggara Timur', 'PTN'),
  ('Universitas Tanjungpura', 'UNTAN', 'Pontianak', 'Kalimantan Barat', 'PTN'),
  ('Universitas Palangka Raya', 'UPR', 'Palangka Raya', 'Kalimantan Tengah', 'PTN'),
  ('Universitas Lambung Mangkurat', 'ULM', 'Banjarmasin', 'Kalimantan Selatan', 'PTN'),
  ('Universitas Mulawarman', 'UNMUL', 'Samarinda', 'Kalimantan Timur', 'PTN'),
  ('Universitas Borneo Tarakan', 'UBT', 'Tarakan', 'Kalimantan Utara', 'PTN'),
  ('Universitas Sam Ratulangi', 'UNSRAT', 'Manado', 'Sulawesi Utara', 'PTN'),
  ('Universitas Tadulako', 'UNTAD', 'Palu', 'Sulawesi Tengah', 'PTN'),
  ('Universitas Halu Oleo', 'UHO', 'Kendari', 'Sulawesi Tenggara', 'PTN'),
  ('Universitas Sembilanbelas November Kolaka', 'USN', 'Kolaka', 'Sulawesi Tenggara', 'PTN'),
  ('Universitas Sulawesi Barat', 'UNSULBAR', 'Majene', 'Sulawesi Barat', 'PTN'),
  ('Universitas Khairun', 'UNKHAIR', 'Ternate', 'Maluku Utara', 'PTN'),
  ('Universitas Pattimura', 'UNPATTI', 'Ambon', 'Maluku', 'PTN'),
  ('Universitas Cenderawasih', 'UNCEN', 'Jayapura', 'Papua', 'PTN'),
  ('Universitas Musamus Merauke', 'UNMUS', 'Merauke', 'Papua Selatan', 'PTN'),
  ('Universitas Papua', 'UNIPA', 'Manokwari', 'Papua Barat', 'PTN'),
  ('Institut Teknologi Sumatera', 'ITERA', 'Lampung Selatan', 'Lampung', 'PTN'),
  ('Institut Teknologi Kalimantan', 'ITK', 'Balikpapan', 'Kalimantan Timur', 'PTN'),
  ('Institut Teknologi Bacharuddin Jusuf Habibie', 'ITH', 'Parepare', 'Sulawesi Selatan', 'PTN'),
  ('Institut Seni Indonesia Yogyakarta', 'ISI Yogyakarta', 'Bantul', 'DI Yogyakarta', 'PTN'),
  ('Institut Seni Indonesia Surakarta', 'ISI Surakarta', 'Surakarta', 'Jawa Tengah', 'PTN'),
  ('Institut Seni Indonesia Denpasar', 'ISI Denpasar', 'Denpasar', 'Bali', 'PTN'),
  ('Institut Seni Indonesia Padangpanjang', 'ISI Padangpanjang', 'Padang Panjang', 'Sumatera Barat', 'PTN'),
  ('Institut Seni Budaya Indonesia Bandung', 'ISBI Bandung', 'Bandung', 'Jawa Barat', 'PTN'),
  ('Institut Seni Budaya Indonesia Aceh', 'ISBI Aceh', 'Aceh Besar', 'Aceh', 'PTN'),
  ('Institut Seni Budaya Indonesia Tanah Papua', 'ISBI Tanah Papua', 'Jayapura', 'Papua', 'PTN'),
  ('Politeknik Negeri Jakarta', 'PNJ', 'Depok', 'Jawa Barat', 'PTN'),
  ('Politeknik Negeri Bandung', 'POLBAN', 'Bandung Barat', 'Jawa Barat', 'PTN'),
  ('Politeknik Negeri Semarang', 'POLINES', 'Semarang', 'Jawa Tengah', 'PTN'),
  ('Politeknik Negeri Malang', 'POLINEMA', 'Malang', 'Jawa Timur', 'PTN'),
  ('Politeknik Negeri Medan', 'POLMED', 'Medan', 'Sumatera Utara', 'PTN'),
  ('Politeknik Negeri Padang', 'PNP', 'Padang', 'Sumatera Barat', 'PTN'),
  ('Politeknik Negeri Sriwijaya', 'POLSRI', 'Palembang', 'Sumatera Selatan', 'PTN'),
  ('Politeknik Negeri Lampung', 'POLINELA', 'Bandar Lampung', 'Lampung', 'PTN'),
  ('Politeknik Negeri Bali', 'PNB', 'Badung', 'Bali', 'PTN'),
  ('Politeknik Negeri Ujung Pandang', 'PNUP', 'Makassar', 'Sulawesi Selatan', 'PTN'),
  ('Politeknik Negeri Manado', 'POLIMDO', 'Manado', 'Sulawesi Utara', 'PTN'),
  ('Politeknik Negeri Ambon', 'POLNAM', 'Ambon', 'Maluku', 'PTN'),
  ('Politeknik Negeri Kupang', 'PNK', 'Kupang', 'Nusa Tenggara Timur', 'PTN'),
  ('Politeknik Negeri Pontianak', 'POLNEP', 'Pontianak', 'Kalimantan Barat', 'PTN'),
  ('Politeknik Negeri Samarinda', 'POLNES', 'Samarinda', 'Kalimantan Timur', 'PTN'),
  ('Politeknik Negeri Banjarmasin', 'POLIBAN', 'Banjarmasin', 'Kalimantan Selatan', 'PTN'),
  ('Politeknik Negeri Balikpapan', 'POLTEKBA', 'Balikpapan', 'Kalimantan Timur', 'PTN'),
  ('Politeknik Negeri Jember', 'POLIJE', 'Jember', 'Jawa Timur', 'PTN'),
  ('Politeknik Negeri Madiun', 'PNM', 'Madiun', 'Jawa Timur', 'PTN'),
  ('Politeknik Negeri Banyuwangi', 'POLIWANGI', 'Banyuwangi', 'Jawa Timur', 'PTN'),
  ('Politeknik Negeri Madura', 'POLTERA', 'Sampang', 'Jawa Timur', 'PTN'),
  ('Politeknik Elektronika Negeri Surabaya', 'PENS', 'Surabaya', 'Jawa Timur', 'PTN'),
  ('Politeknik Perkapalan Negeri Surabaya', 'PPNS', 'Surabaya', 'Jawa Timur', 'PTN'),
  ('Politeknik Negeri Cilacap', 'PNC', 'Cilacap', 'Jawa Tengah', 'PTN'),
  ('Politeknik Maritim Negeri Indonesia', 'POLIMARIN', 'Semarang', 'Jawa Tengah', 'PTN'),
  ('Politeknik Negeri Indramayu', 'POLINDRA', 'Indramayu', 'Jawa Barat', 'PTN'),
  ('Politeknik Negeri Subang', 'POLSUB', 'Subang', 'Jawa Barat', 'PTN'),
  ('Politeknik Manufaktur Negeri Bandung', 'POLMAN Bandung', 'Bandung', 'Jawa Barat', 'PTN'),
  ('Politeknik Negeri Media Kreatif', 'POLIMEDIA', 'Jakarta Selatan', 'DKI Jakarta', 'PTN'),
  ('Politeknik Negeri Lhokseumawe', 'PNL', 'Lhokseumawe', 'Aceh', 'PTN'),
  ('Politeknik Negeri Batam', 'POLIBATAM', 'Batam', 'Kepulauan Riau', 'PTN'),
  ('Politeknik Negeri Bengkalis', 'POLBENG', 'Bengkalis', 'Riau', 'PTN'),
  ('Politeknik Manufaktur Negeri Bangka Belitung', 'POLMAN Babel', 'Bangka', 'Kepulauan Bangka Belitung', 'PTN'),
  ('Politeknik Pertanian Negeri Payakumbuh', 'POLITANI Payakumbuh', 'Lima Puluh Kota', 'Sumatera Barat', 'PTN'),
  ('Politeknik Pertanian Negeri Kupang', 'POLITANI Kupang', 'Kupang', 'Nusa Tenggara Timur', 'PTN'),
  ('Politeknik Pertanian Negeri Samarinda', 'POLITANI Samarinda', 'Samarinda', 'Kalimantan Timur', 'PTN'),
  ('Politeknik Pertanian Negeri Pangkajene Kepulauan', 'POLIPANGKEP', 'Pangkep', 'Sulawesi Selatan', 'PTN'),
  ('Politeknik Negeri Tanah Laut', 'POLITALA', 'Tanah Laut', 'Kalimantan Selatan', 'PTN'),
  ('Politeknik Negeri Ketapang', 'POLITAP', 'Ketapang', 'Kalimantan Barat', 'PTN'),
  ('Politeknik Negeri Sambas', 'POLTESA', 'Sambas', 'Kalimantan Barat', 'PTN'),
  ('Politeknik Negeri Nusa Utara', 'POLNUSTAR', 'Kepulauan Sangihe', 'Sulawesi Utara', 'PTN'),
  ('Politeknik Negeri Fakfak', 'POLINEF', 'Fakfak', 'Papua Barat', 'PTN'),
  ('Politeknik Negeri Nunukan', 'PNN', 'Nunukan', 'Kalimantan Utara', 'PTN'),
  ('Politeknik Perikanan Negeri Tual', 'POLIKANT', 'Tual', 'Maluku', 'PTN'),
  ('Politeknik Negeri Sakti Alam Kerinci', 'POLTESAK', 'Kerinci', 'Jambi', 'PTN');

-- 10. Daftar PTS (tambahkan sendiri sesuai kebutuhan)
INSERT INTO campuses (name, short_name, city, province, type) VALUES
  ('Institut Teknologi Del', 'IT Del', 'Toba', 'Sumatera Utara', 'PTS'),
  ('Universitas HKBP Nommensen', 'UHN', 'Medan', 'Sumatera Utara', 'PTS'),
  ('Universitas Medan Area', 'UMA', 'Medan', 'Sumatera Utara', 'PTS'),
  ('Universitas Prima Indonesia', 'UNPRI', 'Medan', 'Sumatera Utara', 'PTS'),
  ('Universitas Islam Sumatera Utara', 'UISU', 'Medan', 'Sumatera Utara', 'PTS'),
  ('Universitas Bina Nusantara', 'BINUS', 'Jakarta Barat', 'DKI Jakarta', 'PTS'),
  ('Universitas Trisakti', 'USAKTI', 'Jakarta Barat', 'DKI Jakarta', 'PTS'),
  ('Universitas Mercu Buana', 'UMB', 'Jakarta Barat', 'DKI Jakarta', 'PTS'),
  ('Universitas Gunadarma', 'UG', 'Depok', 'Jawa Barat', 'PTS'),
  ('Universitas Pelita Harapan', 'UPH', 'Tangerang', 'Banten', 'PTS'),
  ('Universitas Telkom', 'Tel-U', 'Bandung', 'Jawa Barat', 'PTS'),
  ('Universitas Katolik Parahyangan', 'UNPAR', 'Bandung', 'Jawa Barat', 'PTS'),
  ('Universitas Islam Indonesia', 'UII', 'Sleman', 'DI Yogyakarta', 'PTS'),
  ('Universitas Atma Jaya Yogyakarta', 'UAJY', 'Sleman', 'DI Yogyakarta', 'PTS'),
  ('Universitas Muhammadiyah Yogyakarta', 'UMY', 'Bantul', 'DI Yogyakarta', 'PTS'),
  ('Universitas Kristen Satya Wacana', 'UKSW', 'Salatiga', 'Jawa Tengah', 'PTS'),
  ('Universitas Muhammadiyah Malang', 'UMM', 'Malang', 'Jawa Timur', 'PTS'),
  ('Universitas Kristen Petra', 'UK Petra', 'Surabaya', 'Jawa Timur', 'PTS');

UPDATE campuses SET created_at = NOW(), updated_at = NOW();

-- 11. Akun demo (kata sandi semua akun: password)
--     Nilai password di bawah adalah hasil enkripsi bcrypt dari kata "password".
UPDATE campuses SET status = 'uji_coba', trial_ends_at = CURRENT_DATE + 30, email_domain = 'usu.ac.id'
 WHERE name = 'Universitas Sumatera Utara';
UPDATE campuses SET status = 'menunggu' WHERE name = 'Universitas Andalas';

INSERT INTO users (name, email, password, role, campus_id, nim, prodi, phone, position, email_verified_at, created_at, updated_at) VALUES
  ('Admin CELS', 'admin@cels.test', '$2y$12$nseEDrn8Pbax5ldhfy4ETuBS57JaPz9N5EF6bXM0qK49ccHSGdQOq', 'admin_cels', NULL, NULL, NULL, NULL, NULL, NOW(), NOW(), NOW()),
  ('Rina Siregar', 'pdlk@usu.ac.id', '$2y$12$nseEDrn8Pbax5ldhfy4ETuBS57JaPz9N5EF6bXM0qK49ccHSGdQOq', 'admin_kampus',
     (SELECT id FROM campuses WHERE name = 'Universitas Sumatera Utara'), NULL, NULL, '081200000001', 'Kepala Unit PDLK', NOW(), NOW(), NOW()),
  ('Budi Santoso', 'kemahasiswaan@unand.ac.id', '$2y$12$nseEDrn8Pbax5ldhfy4ETuBS57JaPz9N5EF6bXM0qK49ccHSGdQOq', 'admin_kampus',
     (SELECT id FROM campuses WHERE name = 'Universitas Andalas'), NULL, NULL, '081200000002', 'Staf Kemahasiswaan', NULL, NOW(), NOW()),
  ('Andi Pratama', 'mahasiswa@students.usu.ac.id', '$2y$12$nseEDrn8Pbax5ldhfy4ETuBS57JaPz9N5EF6bXM0qK49ccHSGdQOq', 'mahasiswa',
     (SELECT id FROM campuses WHERE name = 'Universitas Sumatera Utara'), '221402001', 'Teknologi Informasi', NULL, NULL, NOW(), NOW(), NOW()),
  ('Sari Lubis', 'sari@students.usu.ac.id', '$2y$12$nseEDrn8Pbax5ldhfy4ETuBS57JaPz9N5EF6bXM0qK49ccHSGdQOq', 'mahasiswa',
     (SELECT id FROM campuses WHERE name = 'Universitas Sumatera Utara'), '221402002', 'Ilmu Komputer', NULL, NULL, NULL, NOW(), NOW());

-- 12. Pengajuan kampus (USU disetujui, UNAND menunggu)
INSERT INTO campus_registrations (campus_id, user_id, pic_name, pic_position, pic_phone, institution_email, document_path, status, reviewed_at, created_at, updated_at)
SELECT u.campus_id, u.id, u.name, u.position, u.phone, u.email, 'campus-documents/contoh-surat-tugas.pdf',
       CASE WHEN u.email = 'pdlk@usu.ac.id' THEN 'disetujui' ELSE 'menunggu' END,
       CASE WHEN u.email = 'pdlk@usu.ac.id' THEN NOW() END,
       NOW(), NOW()
  FROM users u
 WHERE u.role = 'admin_kampus';

-- 13. Contoh program dari USU
INSERT INTO programs (campus_id, created_by, title, type, description, quota, deadline, requirements, partner_info, is_open, created_at, updated_at)
SELECT u.campus_id, u.id, p.title, p.type, p.description, p.quota, CURRENT_DATE + 21, p.requirements, p.partner_info, TRUE, NOW(), NOW()
  FROM users u
 CROSS JOIN (VALUES
   ('Magang Software Engineer', 'magang', 'Program magang 4 bulan di bidang pengembangan perangkat lunak.', 10,
    E'Mahasiswa aktif minimal semester 5\nMenguasai dasar pemrograman web\nIPK minimal 3.00', 'Mitra: PT Teknologi Nusantara (www.contoh-mitra.co.id)'),
   ('Kerja Praktik Jaringan Komputer', 'kerja_praktik', 'Kerja praktik di unit infrastruktur jaringan selama 2 bulan.', 5,
    E'Mahasiswa aktif minimal semester 4\nMemahami dasar jaringan komputer', 'Mitra: Dinas Kominfo Kota Medan'),
   ('Asisten Riset Kecerdasan Buatan', 'riset', 'Membantu riset dosen di bidang machine learning.', 3,
    E'Mahasiswa aktif minimal semester 5\nMenguasai Python', NULL)
 ) AS p(title, type, description, quota, requirements, partner_info)
 WHERE u.email = 'pdlk@usu.ac.id';

COMMIT;

-- Cek hasil
SELECT type, status, COUNT(*) AS jumlah FROM campuses GROUP BY type, status ORDER BY type, status;
SELECT name, email, role FROM users ORDER BY id;
