/* =========================================================
   CELS - skrip umum untuk semua halaman
   1. Tombol mata (tampilkan/sembunyikan) di semua kolom kata sandi
   2. Tanda * merah otomatis di label kolom wajib
   3. Validasi langsung + pesan Bahasa Indonesia di bawah kolom
   4. Pengisian bertahap untuk form bertanda data-step-form
   ========================================================= */
(function () {
    'use strict';

    /* ---------- Aturan khusus (dipasang lewat atribut data-rule) ---------- */
    var RULES = {
        // Nama orang: diawali huruf, hanya huruf/spasi/titik/koma/petik/tanda hubung
        'person-name': function (v) {
            if (!/^\p{L}/u.test(v)) return 'Harus diawali huruf.';
            if (!/^[\p{L}\s.,'\-]+$/u.test(v)) return 'Hanya boleh berisi huruf, spasi, titik, koma, tanda petik, dan tanda hubung.';
            return '';
        },
        // Teks umum (jabatan, prodi, nama kampus, kota): diawali huruf,
        // hanya huruf, angka, spasi, dan tanda . , ' - / ( )
        'starts-letter': function (v) {
            if (!/^\p{L}/u.test(v)) return 'Harus diawali huruf.';
            if (!/^[\p{L}\p{N}\s.,'\-\/()]+$/u.test(v)) return "Hanya boleh berisi huruf, angka, spasi, dan tanda . , ' - / ( )";
            return '';
        },
        // Nomor HP Indonesia: 08xx / 628xx / +628xx
        'phone-id': function (v) {
            return /^(\+62|62|0)8[0-9]{8,11}$/.test(v) ? '' : 'Gunakan nomor HP Indonesia, contoh: 081234567890.';
        },
        // Email kampus/institusi berakhiran .ac.id
        'email-acid': function (v) {
            return /\.ac\.id$/i.test(v) ? '' : 'Gunakan email kampus yang berakhiran .ac.id.';
        },
        // Judul program: diawali huruf/angka, minimal 3 huruf, tanda terbatas
        'program-title': function (v) {
            if (!/^[\p{L}\p{N}]/u.test(v)) return 'Harus diawali huruf atau angka.';
            if (!/^[\p{L}\p{N}\s.,'\-\/()&:]+$/u.test(v)) return "Hanya boleh berisi huruf, angka, spasi, dan tanda . , ' - / ( ) & :";
            if ((v.match(/\p{L}/gu) || []).length < 3) return 'Harus memuat minimal 3 huruf.';
            return '';
        },

                // Tanggal hari/bulan/tahun, mis. 31/12/2026 (dipakai kolom tanggal otomatis)
        'date-id': function (v, el) {
            if (!/^\d{2}\/\d{2}\/\d{4}$/.test(v)) return 'Gunakan format hari/bulan/tahun, contoh: 31/12/2026.';
            var iso = idToIso(v);
            if (!iso) return 'Tanggal tidak valid.';
            if (el.dataset.min && iso < el.dataset.min) return 'Tanggal paling cepat ' + isoToId(el.dataset.min) + '.';
            if (el.dataset.max && iso > el.dataset.max) return 'Tanggal paling lambat ' + isoToId(el.dataset.max) + '.';
            return '';
        },

        // Teks panjang (deskripsi, syarat): harus berisi kalimat, bukan hanya simbol
        'meaningful-text': function (v, el) {
            var min = Number(el.dataset.minLetters || 10);
            return (v.match(/\p{L}/gu) || []).length < min
                ? 'Harus berisi kalimat yang jelas (minimal ' + min + ' huruf).' : '';
        }
    };

    /* ---------- Pesan bawaan browser dalam Bahasa Indonesia ---------- */
    function nativeMessage(el) {
        var v = el.validity;
        if (v.valueMissing) {
            if (el.type === 'file') return 'Silakan pilih file terlebih dahulu.';
            if (el.type === 'checkbox' || el.type === 'radio') return 'Silakan pilih salah satu.';
            if (el.tagName === 'SELECT') return 'Silakan pilih salah satu dari daftar.';
            return 'Kolom ini wajib diisi.';
        }
        if (v.typeMismatch) {
            if (el.type === 'email') return 'Masukkan alamat email yang valid, contoh: nama@kampus.ac.id';
            if (el.type === 'url') return 'Masukkan alamat URL yang valid, contoh: https://contoh.ac.id';
            return 'Format isian tidak sesuai.';
        }
        if (v.patternMismatch) return el.title || 'Format isian tidak sesuai.';
        if (v.tooShort) return 'Minimal ' + el.minLength + ' karakter (sekarang ' + el.value.length + ' karakter).';
        if (v.tooLong) return 'Maksimal ' + el.maxLength + ' karakter.';
        if (v.rangeUnderflow) return 'Nilai minimal adalah ' + el.min + '.';
        if (v.rangeOverflow) return 'Nilai maksimal adalah ' + el.max + '.';
        if (v.stepMismatch) return 'Nilai tidak sesuai kelipatan yang diizinkan.';
        if (v.badInput) return el.type === 'number' ? 'Masukkan angka yang valid.' : 'Isian tidak valid.';
        return '';
    }

    /* Cek satu kolom. Mengembalikan pesan error ('' jika benar). */
    function validate(el) {
        if (!el.setCustomValidity || el.disabled) return '';
        el.setCustomValidity('');
        var msg = nativeMessage(el);
        var value = (el.value || '').trim();

        if (!msg && value && el.dataset.rule && RULES[el.dataset.rule]) {
            msg = RULES[el.dataset.rule](value, el);
        }
        if (!msg && el.dataset.match && el.form) {
            var other = el.form.querySelector('[name="' + el.dataset.match + '"]');
            if (other && el.value !== other.value) msg = 'Konfirmasi kata sandi tidak cocok.';
        }
        el.setCustomValidity(msg);
        return msg;
    }

    /* ---------- Tampilan pesan di bawah kolom ---------- */
    function anchorOf(el) {
        // Taruh pesan setelah input-group (tombol mata) atau Tom Select bila ada
        if (el.tomselect) return el.tomselect.wrapper;
        return el.closest('.input-group') || el;
    }

    function feedbackOf(el) {
        var anchor = anchorOf(el);
        var next = anchor.nextElementSibling;
        if (next && next.classList.contains('invalid-feedback')) return next;
        var box = document.createElement('div');
        box.className = 'invalid-feedback';
        anchor.parentNode.insertBefore(box, anchor.nextSibling);
        return box;
    }

    function show(el, msg) {
        var target = el.tomselect ? el.tomselect.wrapper : el;
        var box = feedbackOf(el);
        var filled = el.type === 'file' ? el.files.length > 0 : (el.value || '').trim() !== '';

        target.classList.toggle('is-invalid', !!msg);
        target.classList.toggle('is-valid', !msg && filled);
        box.textContent = msg;
        box.classList.toggle('d-block', !!msg);
    }

    /* ---------- Tanda * pada label kolom wajib ---------- */
    function labelOf(el) {
        if (el.id) {
            var byFor = document.querySelector('label[for="' + el.id + '"]');
            if (byFor) return byFor;
        }
        var box = el.closest('.mb-3, [class*="col-"], .form-group');
        return box ? box.querySelector('label.form-label') : null;
    }

    function markRequired(el) {
        var label = labelOf(el);
        if (!label) return;
        var mark = label.querySelector('.required-mark');
        var hasManualStar = !mark && label.textContent.indexOf('*') !== -1;
        if (el.required && !mark && !hasManualStar) {
            mark = document.createElement('span');
            mark.className = 'required-mark text-danger ms-1';
            mark.setAttribute('aria-hidden', 'true');
            mark.textContent = '*';
            label.appendChild(mark);
        } else if (!el.required && mark) {
            mark.remove();
        }
    }

    function fieldsOf(form) {
        return Array.prototype.filter.call(form.elements, function (el) {
            return /^(INPUT|SELECT|TEXTAREA)$/.test(el.tagName)
                && ['hidden', 'submit', 'button', 'reset', 'image'].indexOf(el.type) === -1
                && !el.classList.contains('ts-control-input')      // kolom ketik milik Tom Select
                && !el.classList.contains('date-native')           // kolom tanggal asli (tersembunyi)
                && !el.closest('.ts-wrapper');
        });
    }

    function isVisible(el) {
        var target = el.tomselect ? el.tomselect.wrapper : el;
        return !!(target.offsetWidth || target.offsetHeight || target.getClientRects().length);
    }

    /* ---------- Pengisian bertahap ---------- */
    function setLocked(el, locked) {
        if (el.dataset.stepLocked === String(locked)) return;
        el.dataset.stepLocked = String(locked);
        if (el.tomselect) {
            locked ? el.tomselect.lock() : el.tomselect.unlock();
            el.tomselect.wrapper.classList.toggle('step-locked', locked);
        } else {
            el.readOnly = locked && el.tagName !== 'SELECT';
            el.classList.toggle('step-locked', locked);
        }
        el.tabIndex = locked ? -1 : 0;
        el.title = locked ? 'Lengkapi kolom sebelumnya terlebih dahulu.' : (el.dataset.origTitle || '');
    }

    function refresh(form) {
        var stepMode = form.hasAttribute('data-step-form');
        var blocked = false;

        fieldsOf(form).forEach(function (el) {
            if (el.dataset.origTitle === undefined) el.dataset.origTitle = el.title || '';
            markRequired(el);
            if (!isVisible(el)) return;

            if (el.disabled || (/^(checkbox|radio)$/.test(el.type) && !el.required)) {   // kolom terkunci / centang opsional: tanpa tanda
                el.classList.remove('is-valid', 'is-invalid');
                return;
            }

            if (stepMode) setLocked(el, blocked);
            if (blocked) { show(el, ''); return; }

            var msg = validate(el);
            if (el.dataset.touched) show(el, msg);
            if (stepMode && msg) blocked = true;
        });

        if (stepMode) {
            form.querySelectorAll('button[type="submit"], button:not([type])').forEach(function (btn) {
                if (btn.closest('.input-group')) return;           // bukan tombol mata
                btn.disabled = blocked;
            });
        }
    }

    function formOf(el) { return el && el.form ? el.form : null; }

    /* ---------- Kolom angka: tidak bisa melebihi batas (atribut max) ---------- */
    function flashNote(el, text) {
        var anchor = anchorOf(el);
        var note = anchor.parentNode.querySelector('.limit-note');
        if (!note) {
            note = document.createElement('div');
            note.className = 'limit-note form-text text-warning fw-semibold';
            anchor.parentNode.insertBefore(note, anchor.nextSibling);
        }
        note.textContent = text;
        clearTimeout(note._timer);
        note._timer = setTimeout(function () { note.textContent = ''; }, 2500);
    }

    function clampNumber(el) {
        if (el.type !== 'number' || el.value === '') return;
        var value = Number(el.value);
        if (el.max !== '' && value > Number(el.max)) {
            el.value = el.max;
            flashNote(el, 'Maksimal ' + Number(el.max).toLocaleString('id-ID') + '.');
        }
    }

    // Kolom angka bulat: tolak huruf e, tanda +/-, dan desimal
    document.addEventListener('keydown', function (e) {
        var el = e.target;
        if (el.type !== 'number') return;
        var integerOnly = el.step === '' || el.step === '1';
        if (['e', 'E', '+', '-'].indexOf(e.key) !== -1 || (integerOnly && (e.key === '.' || e.key === ','))) {
            e.preventDefault();
        }
    }, true);

        /* ---------- Kolom tanggal format hari/bulan/tahun ----------
       Kolom <input type="date"> bawaan browser mengikuti bahasa Chrome (bisa mm/dd/yyyy).
       Di sini diganti kolom ketik hh/bb/tttt + tombol kalender.
       Kolom asli tetap ada (tersembunyi) dan tetap dikirim ke server dengan format 2026-12-31. */
    function pad2(n) { return (n < 10 ? '0' : '') + n; }

    function isoToId(iso) {
        var m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso || '');
        return m ? m[3] + '/' + m[2] + '/' + m[1] : '';
    }

    function idToIso(text) {
        var m = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(text || '');
        if (!m) return '';
        var d = +m[1], mo = +m[2], y = +m[3];
        var date = new Date(y, mo - 1, d);
        if (date.getFullYear() !== y || date.getMonth() !== mo - 1 || date.getDate() !== d) return '';
        return y + '-' + pad2(mo) + '-' + pad2(d);
    }

    function enhanceDate(native) {
        if (native.dataset.dateReady) return;
        native.dataset.dateReady = '1';

        var text = document.createElement('input');
        text.type = 'text';
        text.className = native.className;
        text.placeholder = 'dd/mm/yyyy';
        text.inputMode = 'numeric';
        text.maxLength = 10;
        text.autocomplete = 'off';
        text.title = native.title || 'Format: dd/mm/yyyy';
        text.required = native.required;
        text.dataset.rule = 'date-id';
        text.dataset.min = native.min || '';
        text.dataset.max = native.max || '';
        text.value = isoToId(native.value);
        if (native.id) { text.id = native.id; native.id = native.id + '_asli'; }
        native.required = false;                      // yang wajib diisi sekarang kolom ketiknya

        var group = document.createElement('div');
        group.className = 'input-group' + (native.classList.contains('form-control-sm') ? ' input-group-sm' : '');
        native.parentNode.insertBefore(group, native);
        group.appendChild(text);

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-outline-secondary position-relative';
        button.title = 'Pilih dari kalender';
        button.innerHTML = '<i class="bi bi-calendar3"></i>';
        group.appendChild(button);

        native.classList.add('date-native');
        native.tabIndex = -1;
        native.setAttribute('aria-hidden', 'true');
        button.appendChild(native);                   // kalender muncul tepat di bawah tombol

        button.addEventListener('click', function () {
            if (text.dataset.stepLocked === 'true') return;
            try { native.showPicker(); } catch (err) { native.focus(); }
        });

        // Dipilih dari kalender -> tampilkan hh/bb/tttt
        native.addEventListener('change', function () {
            text.value = isoToId(native.value);
            text.dataset.touched = '1';
            if (text.form) refresh(text.form);
        });

        // Diketik -> tambah garis miring otomatis, lalu isi kolom asli
        text.addEventListener('input', function () {
            var digits = text.value.replace(/\D/g, '').slice(0, 8);
            var out = digits.slice(0, 2);
            if (digits.length > 2) out += '/' + digits.slice(2, 4);
            if (digits.length > 4) out += '/' + digits.slice(4);
            text.value = out;
            native.value = idToIso(out);
            if (text.form) refresh(text.form);
        });
    }

        /* ---------- Baris tabel bisa diklik (atribut data-href pada <tr>) ---------- */
    function rowTarget(e) {
        var row = e.target.closest('tr[data-href]');
        if (!row) return null;
        // Klik pada tombol/link/isian di dalam baris tetap menjalankan fungsinya sendiri
        if (e.target.closest('a, button, input, select, textarea, label, form')) return null;
        return row;
    }

    document.addEventListener('click', function (e) {
        var row = rowTarget(e);
        if (!row) return;
        if (e.ctrlKey || e.metaKey) window.open(row.dataset.href, '_blank');   // Ctrl+klik = tab baru
        else window.location.href = row.dataset.href;
    });

    document.addEventListener('auxclick', function (e) {                      // klik tengah = tab baru
        var row = rowTarget(e);
        if (row && e.button === 1) window.open(row.dataset.href, '_blank');
    });

    document.addEventListener('keydown', function (e) {                       // Enter pada baris
        if (e.key === 'Enter' && e.target.matches && e.target.matches('tr[data-href]')) {
            window.location.href = e.target.dataset.href;
        }
    });

    /* ---------- Tombol lihat kata sandi ---------- */
    function addPasswordToggle(input) {
        if (input.dataset.toggleReady) return;
        input.dataset.toggleReady = '1';

        var group = document.createElement('div');
        group.className = 'input-group';
        input.parentNode.insertBefore(group, input);
        group.appendChild(input);

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-outline-secondary';
        button.setAttribute('aria-label', 'Tampilkan kata sandi');
        button.title = 'Tampilkan kata sandi';
        button.innerHTML = '<i class="bi bi-eye"></i>';
        group.appendChild(button);

        button.addEventListener('click', function () {
            var showPw = input.type === 'password';
            input.type = showPw ? 'text' : 'password';
            button.innerHTML = showPw ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            var label = showPw ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi';
            button.setAttribute('aria-label', label);
            button.title = label;
            input.focus();
        });
    }

    /* ---------- Event ---------- */
    // Pesan browser (saat tombol kirim ditekan) ikut Bahasa Indonesia
    document.addEventListener('invalid', function (e) {
        validate(e.target);
        e.target.dataset.touched = '1';
        show(e.target, e.target.validationMessage);
    }, true);

    ['input', 'change'].forEach(function (type) {
        document.addEventListener(type, function (e) {
            if (type === 'input') clampNumber(e.target);
            var form = formOf(e.target);
            if (!form) return;
            // Kolom dengan aturan khusus langsung diperingatkan saat diketik
            if (type === 'change' || e.target.dataset.rule || e.target.dataset.match || e.target.dataset.touched) {
                e.target.dataset.touched = '1';
            }
            refresh(form);
        }, true);
    });

    // Tandai kolom "sudah disentuh" saat ditinggalkan, supaya pesannya muncul
    document.addEventListener('focusout', function (e) {
        var form = formOf(e.target);
        if (!form || !/^(INPUT|SELECT|TEXTAREA)$/.test(e.target.tagName)) return;
        if (e.target.dataset.stepLocked === 'true') return;
        e.target.dataset.touched = '1';
        refresh(form);
    }, true);

    // Kolom terkunci tidak bisa dipilih/diklik
    ['mousedown', 'click', 'keydown'].forEach(function (type) {
        document.addEventListener(type, function (e) {
            if (!e.target.dataset || e.target.dataset.stepLocked !== 'true') return;
            if (type === 'keydown' && e.key === 'Tab') return;
            e.preventDefault();
        }, true);
    });

    function init() {
        document.querySelectorAll('input[type="password"]').forEach(addPasswordToggle);
        document.querySelectorAll('input[type="date"]').forEach(enhanceDate);
        document.querySelectorAll('tr[data-href]').forEach(function (row) {
            row.tabIndex = 0;                                 // bisa dipilih dengan tombol Tab
            row.setAttribute('role', 'link');
        });

        document.querySelectorAll('form').forEach(function (form) {
            // Kolom yang sudah terisi (mis. setelah gagal validasi server) langsung dicek
            fieldsOf(form).forEach(function (el) {
                if ((el.value || '').trim() !== '' && el.type !== 'password') el.dataset.touched = '1';
            });
            if (form.hasAttribute('data-step-form') && !form.querySelector('.step-hint')) {
                var hint = document.createElement('p');
                hint.className = 'step-hint small text-muted mb-3';
                hint.innerHTML = '<i class="bi bi-info-circle me-1"></i>Kolom bertanda <span class="text-danger">*</span> wajib diisi. '
                    + 'Isi berurutan dari atas; kolom berikutnya terbuka setelah kolom sebelumnya benar.';
                form.insertBefore(hint, form.firstElementChild ? form.firstElementChild.nextSibling : null);
            }
            refresh(form);
        });
    }

    // Bisa dipanggil dari halaman lain, mis. setelah menampilkan kolom tambahan
    window.CELS = { refresh: refresh };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
