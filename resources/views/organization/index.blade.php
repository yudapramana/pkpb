<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Organisasi - {{ config('organization.title') }}</title>
    <meta name="description" content="Struktur Organisasi {{ config('organization.title') }}">
    <link rel="canonical" href="{{ URL::current() }}">

    <link href="{{ asset('arsha/assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('arsha/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Jost:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link href="{{ asset('arsha/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('arsha/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <style>
        :root {
            --org-bg: #ebe5d6;
            --org-panel: #f3efe4;
            --org-teal: #24524f;
            --org-teal-soft: #3d726e;
            --org-line: #5b5b5b;
            --org-text: #1f2a2a;
            --org-muted: #5f6b6a;
            --org-card: #ffffff;
        }

        body {
            background: var(--org-bg);
            color: var(--org-text);
            font-family: "Poppins", sans-serif;
            font-size: 14px;
        }

        /* ---------- Header ---------- */
        .org-header {
            text-align: center;
            padding: 40px 16px 8px;
        }

        .org-header img {
            width: 84px;
        }

        .org-header__eyebrow {
            font-family: "Jost", sans-serif;
            font-weight: 700;
            font-size: clamp(1.25rem, 3vw, 1.9rem);
            letter-spacing: .02em;
            margin: 16px 0 0;
            text-transform: uppercase;
        }

        .org-header__title {
            font-family: "Jost", sans-serif;
            font-weight: 800;
            color: var(--org-teal);
            font-size: clamp(1.5rem, 4.4vw, 3rem);
            line-height: 1.05;
            text-transform: uppercase;
            margin: 4px auto 12px;
            max-width: 900px;
        }

        .org-header__meta {
            font-weight: 600;
            font-size: .85rem;
            margin: 0;
        }

        .org-header__link {
            margin: 12px 0 0;
            font-size: .8rem;
        }

        .org-header__link a {
            color: var(--org-teal);
        }

        /* Pilihan versi: Penugasan / Sesuai DUK */
        .org-version {
            display: inline-flex;
            margin-top: 14px;
            padding: 3px;
            border-radius: 999px;
            background: rgba(36, 82, 79, .12);
        }

        .org-version a {
            padding: 6px 16px;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            color: var(--org-teal);
            text-decoration: none;
        }

        .org-version a.is-active {
            background: var(--org-teal);
            color: #fff;
        }

        .org-card__note {
            display: inline-block;
            align-self: flex-start;
            margin-top: 4px;
            padding: 1px 8px;
            border-radius: 999px;
            background: #fdf0d5;
            color: #7a4b00;
            font-size: .68rem;
            font-weight: 600;
            line-height: 1.5;
        }

        .org-card--stack .org-card__note {
            align-self: center;
        }

        .org-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
            margin-top: 14px;
        }

        .org-actions .org-version {
            margin-top: 0;
        }

        .org-print {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border: 0;
            border-radius: 999px;
            background: #fff;
            color: var(--org-teal);
            font: inherit;
            font-size: .8rem;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .08);
            cursor: pointer;
        }

        .org-print:hover,
        .org-print:focus-visible {
            background: var(--org-teal);
            color: #fff;
        }

        .org-print[disabled] {
            opacity: .6;
            cursor: progress;
        }

        /* Unduh gambar (PNG/JPG) */
        .org-export {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 3px 3px 3px 14px;
            border-radius: 999px;
            background: rgba(36, 82, 79, .12);
        }

        .org-export__label {
            font-size: .8rem;
            font-weight: 600;
            color: var(--org-teal);
        }

        .org-export__scope {
            border: 0;
            border-radius: 999px;
            padding: 6px 10px;
            font: inherit;
            font-size: .8rem;
            color: var(--org-teal);
            background: #fff;
        }

        .org-export .org-print {
            padding: 6px 14px;
        }

        .org-export__status {
            min-height: 1.2em;
            margin: 8px 0 0;
            font-size: .8rem;
            color: var(--org-teal);
        }

        /* Saat membuat gambar: tata letak lebar tetap dipakai walau dibuka di layar kecil. */
        html.is-exporting body {
            width: 1280px;
        }

        @media print {
            .org-export__status {
                display: none !important;
            }
        }

        /* Tombol & tautan tidak ikut tercetak / masuk PDF. */
        @media print {
            .org-header__link,
            .org-actions {
                display: none !important;
            }
        }

        /* ---------- Kartu pegawai ---------- */
        .org-card {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--org-card);
            border-radius: 999px;
            padding: 6px 18px 6px 6px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .06), 0 2px 8px rgba(0, 0, 0, .04);
            color: inherit;
            text-decoration: none;
            min-width: 0;
            height: 100%;
            transition: box-shadow .15s ease, transform .15s ease;
        }

        a.org-card:hover,
        a.org-card:focus-visible {
            color: inherit;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .12);
            transform: translateY(-1px);
        }

        .org-card--stack {
            flex-direction: column;
            text-align: center;
            border-radius: 16px;
            padding: 14px 10px;
        }

        .org-avatar {
            flex: none;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            background: #dfe7e6;
        }

        .org-card--head .org-avatar {
            width: 60px;
            height: 60px;
        }

        .org-avatar--initials {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: "Jost", sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--org-teal);
        }

        .org-card__body {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .org-card__name {
            font-weight: 600;
            font-size: .88rem;
            line-height: 1.25;
        }

        .org-card__title {
            font-size: .72rem;
            line-height: 1.25;
            color: var(--org-muted);
            margin-top: 2px;
        }

        .org-card__nip {
            font-size: .72rem;
            font-weight: 600;
            margin-top: 3px;
            font-variant-numeric: tabular-nums;
            letter-spacing: .01em;
        }

        /* ---------- Bagan ---------- */
        .org-chart {
            max-width: 1200px;
            margin: 32px auto 0;
            padding: 0 16px;
        }

        .org-root {
            display: flex;
            justify-content: center;
        }

        .org-root .org-card {
            max-width: 360px;
            height: auto;
        }

        /* Batang utama + cabang samping (Sub Bagian) */
        .org-trunk {
            display: grid;
            grid-template-columns: 1fr 2px 1fr;
            align-items: center;
            min-height: 120px;
        }

        .org-trunk__line {
            align-self: stretch;
            background: var(--org-line);
        }

        .org-trunk__side {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-left: 48px;
        }

        .org-trunk__side .org-card {
            position: relative;
            max-width: 340px;
            height: auto;
        }

        .org-trunk__side .org-card::before {
            content: "";
            position: absolute;
            right: 100%;
            top: 50%;
            width: 48px;
            border-top: 2px solid var(--org-line);
        }

        /* Cabang seksi */
        .org-branches {
            display: flex;
            justify-content: center;
        }

        .org-branch {
            flex: 1 1 0;
            min-width: 0;
            position: relative;
            padding: 28px 6px 0;
        }

        .org-branch::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            border-top: 2px solid var(--org-line);
        }

        .org-branch:first-child::before {
            left: 50%;
        }

        .org-branch:last-child::before {
            right: 50%;
        }

        .org-branch::after {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            height: 28px;
            border-left: 2px solid var(--org-line);
        }

        .org-branch__label {
            display: block;
            text-align: center;
            font-family: "Jost", sans-serif;
            font-weight: 700;
            color: var(--org-teal);
            font-size: .8rem;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        /* ---------- Daftar staf per unit ---------- */
        .org-units {
            max-width: 1200px;
            margin: 56px auto 0;
            padding: 0 16px 56px;
            display: grid;
            gap: 24px;
        }

        .org-unit {
            background: var(--org-panel);
            border-radius: 12px;
            padding: 20px 20px 24px;
            scroll-margin-top: 16px;
        }

        .org-unit__head {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            justify-content: space-between;
            gap: 4px 16px;
            margin-bottom: 16px;
        }

        .org-unit__title {
            font-family: "Jost", sans-serif;
            font-weight: 700;
            font-size: clamp(1.15rem, 2.4vw, 1.45rem);
            line-height: 1.2;
            margin: 0;
        }

        .org-unit__subtitle {
            color: var(--org-muted);
            font-size: .8rem;
            margin: 0;
        }

        .org-unit__grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 12px;
        }

        .org-empty {
            text-align: center;
            padding: 80px 16px;
            color: var(--org-muted);
        }

        .org-footer {
            text-align: center;
            font-size: .75rem;
            color: var(--org-muted);
            padding-bottom: 32px;
        }

        /* ---------- Layar kecil: bagan jadi daftar bertingkat ---------- */
        /* Hanya di layar: saat dicetak, bagan tetap memakai tata letak lebar (lihat @page). */
        @media screen and (max-width: 991.98px) {
            html:not(.is-exporting) .org-trunk {
                grid-template-columns: 1fr;
                min-height: 0;
                justify-items: center;
                gap: 0;
            }

            html:not(.is-exporting) .org-trunk__spacer {
                display: none;
            }

            html:not(.is-exporting) .org-trunk__line {
                width: 2px;
                height: 24px;
            }

            html:not(.is-exporting) .org-trunk__side {
                padding: 0;
                width: 100%;
                align-items: center;
            }

            html:not(.is-exporting) .org-trunk__side .org-card::before {
                display: none;
            }

            html:not(.is-exporting) .org-trunk::after {
                content: "";
                width: 2px;
                height: 24px;
                background: var(--org-line);
            }

            html:not(.is-exporting) .org-branches {
                flex-direction: column;
                gap: 12px;
                margin-left: 12px;
                padding: 12px 0 12px 20px;
                border-left: 2px solid var(--org-line);
            }

            html:not(.is-exporting) .org-branch {
                padding: 0;
            }

            html:not(.is-exporting) .org-branch::before {
                display: none;
            }

            html:not(.is-exporting) .org-branch::after {
                top: 50%;
                left: -20px;
                width: 20px;
                height: 0;
                border-left: 0;
                border-top: 2px solid var(--org-line);
            }

            html:not(.is-exporting) .org-branch .org-card--stack {
                flex-direction: row;
                text-align: left;
                border-radius: 999px;
                padding: 6px 18px 6px 6px;
            }

            html:not(.is-exporting) .org-branch__label {
                display: none;
            }

            html:not(.is-exporting) .org-unit__grid {
                grid-template-columns: 1fr;
            }
        }

        /* ---------- Cetak / simpan PDF ---------- */
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        @media print {
            body {
                background: #fff;
                font-size: 12px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .org-header {
                padding-top: 0;
            }

            .org-header img {
                width: 64px;
            }

            .org-chart {
                margin-top: 16px;
                break-inside: avoid;
                break-after: page;
            }

            .org-card {
                box-shadow: none;
                border: 1px solid #c9c9c9;
                transform: none !important;
                break-inside: avoid;
            }

            .org-units {
                margin-top: 32px;
                padding-bottom: 0;
                gap: 14px;
            }

            .org-unit {
                background: #fff;
                border: 1px solid #ddd;
                padding: 12px 14px 14px;
            }

            .org-unit__head {
                break-after: avoid;
            }

            .org-unit__grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .org-footer {
                padding-bottom: 0;
            }
        }
    </style>
</head>

<body>
    <header class="org-header">
        <img src="{{ asset('arsha/assets/img/logokmg2.png') }}" alt="Logo Kementerian Agama">
        <p class="org-header__eyebrow">Struktur Organisasi</p>
        <h1 class="org-header__title">{{ config('organization.title') }}</h1>
        @if (config('organization.legal_basis'))
            <p class="org-header__meta">{{ config('organization.legal_basis') }}</p>
        @endif
        @if ($asOf)
            <p class="org-header__meta">Per {{ $asOf }}</p>
        @endif
        <p class="org-header__meta">Versi: {{ $versionLabel }}</p>

        <div class="org-actions">
            <nav class="org-version" aria-label="Pilih versi">
                @foreach ($versionLinks as $link)
                    <a href="{{ $link['url'] }}" class="{{ $link['active'] ? 'is-active' : '' }}"
                        @if ($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
                @endforeach
            </nav>
            <button type="button" class="org-print" id="org-print">
                <i class="bi bi-printer" aria-hidden="true"></i> Cetak / Simpan PDF
            </button>
            <div class="org-export" role="group" aria-label="Unduh sebagai gambar">
                <label for="org-export-scope" class="org-export__label"><i class="bi bi-image" aria-hidden="true"></i> Unduh gambar</label>
                <select id="org-export-scope" class="org-export__scope">
                    <option value="full">Halaman penuh</option>
                    <option value="chart">Bagan saja</option>
                </select>
                <button type="button" class="org-print js-export" data-format="png">PNG</button>
                <button type="button" class="org-print js-export" data-format="jpg">JPG</button>
            </div>
        </div>
        <p class="org-export__status" id="org-export-status" role="status" aria-live="polite"></p>

        <p class="org-header__link">
            <a href="{{ route('organization.tree', $versionQuery) }}"><i class="bi bi-diagram-3"></i> Bagan tanpa foto</a>
            &nbsp;&middot;&nbsp;
            <a href="{{ route('organization.simple', $versionQuery) }}"><i class="bi bi-file-earmark-word"></i> Versi teks (untuk disalin ke Word)</a>
        </p>
    </header>

    @if (!$root)
        <main class="org-empty">
            <i class="bi bi-diagram-3" style="font-size: 2.5rem"></i>
            <p class="mt-2 mb-0">Data struktur organisasi belum tersedia.</p>
        </main>
    @else
        <main>
            {{-- ===== Bagan ===== --}}
            <section class="org-chart" aria-label="Bagan struktur organisasi">
                <div class="org-root">
                    @if ($root->headPosition)
                        @include('organization._card', ['position' => $root->headPosition])
                    @else
                        <div class="org-card"><span class="org-card__body"><span class="org-card__name">{{ $root->name }}</span></span></div>
                    @endif
                </div>

                @if ($sideUnits->isNotEmpty() || $branchUnits->isNotEmpty())
                    <div class="org-trunk">
                        <span class="org-trunk__spacer"></span>
                        <span class="org-trunk__line"></span>
                        <div class="org-trunk__side">
                            @foreach ($sideUnits as $unit)
                                @if ($unit->headPosition)
                                    @include('organization._card', [
                                        'position' => $unit->headPosition,
                                        'href' => '#unit-' . $unit->id,
                                    ])
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($branchUnits->isNotEmpty())
                    <div class="org-branches">
                        @foreach ($branchUnits as $unit)
                            <div class="org-branch">
                                @if ($unit->headPosition)
                                    @include('organization._card', [
                                        'position' => $unit->headPosition,
                                        'variant' => 'stack',
                                        'href' => '#unit-' . $unit->id,
                                    ])
                                @else
                                    <a class="org-card org-card--stack" href="#unit-{{ $unit->id }}">
                                        <span class="org-card__body">
                                            <span class="org-card__name">{{ $unit->name }}</span>
                                            <span class="org-card__title">Kepala belum ditetapkan</span>
                                        </span>
                                    </a>
                                @endif
                                <span class="org-branch__label">{{ $unit->short_name ?: $unit->name }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- ===== Staf per unit ===== --}}
            <div class="org-units">
                @foreach ($units as $unit)
                    @continue($unit->staffPositions->isEmpty())

                    <section class="org-unit" id="unit-{{ $unit->id }}" aria-labelledby="unit-title-{{ $unit->id }}">
                        <div class="org-unit__head">
                            <h2 class="org-unit__title" id="unit-title-{{ $unit->id }}">{{ $unit->name }}</h2>
                            <p class="org-unit__subtitle">{{ $unit->staffPositions->count() }} pegawai</p>
                        </div>

                        <div class="org-unit__grid">
                            @foreach ($unit->staffPositions as $position)
                                @include('organization._card', ['position' => $position])
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </main>
    @endif

    <footer class="org-footer">
        &copy; {{ date('Y') }} {{ config('organization.title') }}
    </footer>

    <script>
        (function () {
            // Foto memakai loading="lazy": muat semuanya dulu agar ikut tercetak / masuk PDF.
            function loadAllImages() {
                var images = Array.prototype.slice.call(document.images);
                return Promise.all(images.map(function (img) {
                    img.loading = 'eager';
                    if (img.complete) return Promise.resolve();
                    return new Promise(function (resolve) {
                        img.addEventListener('load', resolve, { once: true });
                        img.addEventListener('error', resolve, { once: true });
                    });
                }));
            }

            var button = document.getElementById('org-print');
            if (button) {
                button.addEventListener('click', function () {
                    button.disabled = true;
                    loadAllImages().then(function () {
                        button.disabled = false;
                        // Pilih "Simpan sebagai PDF" di dialog cetak untuk mengekspor PDF.
                        window.print();
                    });
                });
            }

            // Jika dicetak lewat Ctrl+P, tetap minta browser memuat semua foto.
            window.addEventListener('beforeprint', loadAllImages);

            // ---------- Unduh sebagai gambar (PNG / JPG) ----------
            var HTML2CANVAS_URL = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
            var EXPORT_WIDTH = 1280;          // lebar tata letak desktop
            var MAX_PIXELS = 16000000;        // batas aman ukuran kanvas (Safari iOS ~16,7 juta piksel)
            var BACKGROUND = '#ebe5d6';
            var status = document.getElementById('org-export-status');
            var exportButtons = document.querySelectorAll('.js-export');
            var html2canvasPromise = null;

            // Pustaka dimuat hanya saat dibutuhkan.
            function loadHtml2Canvas() {
                if (window.html2canvas) return Promise.resolve(window.html2canvas);
                if (!html2canvasPromise) {
                    html2canvasPromise = new Promise(function (resolve, reject) {
                        var script = document.createElement('script');
                        script.src = HTML2CANVAS_URL;
                        script.async = true;
                        script.onload = function () { resolve(window.html2canvas); };
                        script.onerror = function () {
                            html2canvasPromise = null;
                            reject(new Error('Gagal memuat pustaka html2canvas.'));
                        };
                        document.head.appendChild(script);
                    });
                }
                return html2canvasPromise;
            }

            function setBusy(busy, message) {
                Array.prototype.forEach.call(exportButtons, function (b) { b.disabled = busy; });
                status.textContent = message || '';
            }

            function fileName(ext) {
                var version = @json($version);
                var date = new Date().toISOString().slice(0, 10);
                return 'struktur-organisasi-' + version + '-' + date + '.' + ext;
            }

            function download(canvas, format) {
                var mime = format === 'jpg' ? 'image/jpeg' : 'image/png';
                return new Promise(function (resolve, reject) {
                    canvas.toBlob(function (blob) {
                        if (!blob) return reject(new Error('Gambar terlalu besar untuk dibuat.'));
                        var url = URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = fileName(format);
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
                        resolve();
                    }, mime, 0.92);
                });
            }

            function exportImage(format, scope) {
                var root = document.documentElement;
                var scrollPos = { x: window.scrollX, y: window.scrollY };
                function restore() {
                    root.classList.remove('is-exporting');
                    window.scrollTo(scrollPos.x, scrollPos.y);
                }

                setBusy(true, 'Membuat gambar…');
                root.classList.add('is-exporting');
                window.scrollTo(0, 0); // html2canvas menghitung posisi dari atas halaman

                return Promise.all([loadHtml2Canvas(), loadAllImages(), document.fonts ? document.fonts.ready : null])
                    .then(function (results) {
                        var html2canvas = results[0];
                        var target = document.body;
                        var height = scope === 'chart'
                            ? document.querySelector('.org-chart').getBoundingClientRect().bottom + 32
                            : target.scrollHeight;
                        var scale = Math.min(2, Math.sqrt(MAX_PIXELS / (EXPORT_WIDTH * height)));

                        return html2canvas(target, {
                            backgroundColor: BACKGROUND,
                            scale: scale,
                            width: EXPORT_WIDTH,
                            height: height,
                            windowWidth: EXPORT_WIDTH,
                            x: 0,
                            y: 0,
                            useCORS: true,
                            logging: false,
                            // Tombol, tautan & status tidak ikut; "Bagan saja" melewati daftar staf.
                            ignoreElements: function (el) {
                                if (!el.classList) return false;
                                if (el.classList.contains('org-actions') || el.classList.contains('org-header__link')
                                    || el.classList.contains('org-export__status')) {
                                    return true;
                                }
                                return scope === 'chart' && (el.classList.contains('org-units') || el.classList.contains('org-footer'));
                            },
                            // html2canvas belum mendukung object-fit: ganti foto dengan latar "cover".
                            onclone: function (doc) {
                                Array.prototype.forEach.call(doc.querySelectorAll('img.org-avatar'), function (img) {
                                    var span = doc.createElement('span');
                                    span.className = img.className;
                                    span.style.backgroundImage = 'url("' + img.currentSrc + '")';
                                    span.style.backgroundSize = 'cover';
                                    span.style.backgroundPosition = 'center';
                                    img.replaceWith(span);
                                });
                            }
                        });
                    })
                    .then(function (canvas) {
                        restore();
                        return download(canvas, format);
                    })
                    .then(function () {
                        setBusy(false, 'Gambar berhasil diunduh.');
                        setTimeout(function () { status.textContent = ''; }, 4000);
                    })
                    .catch(function (error) {
                        restore();
                        setBusy(false, (error && error.message) || 'Gagal membuat gambar.');
                    });
            }

            Array.prototype.forEach.call(exportButtons, function (button) {
                button.addEventListener('click', function () {
                    exportImage(button.getAttribute('data-format'), document.getElementById('org-export-scope').value);
                });
            });
        })();
    </script>
</body>

</html>
