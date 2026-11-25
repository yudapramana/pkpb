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

        @media print {
            .org-header__link,
            .org-version {
                display: none;
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
        @media (max-width: 991.98px) {
            .org-trunk {
                grid-template-columns: 1fr;
                min-height: 0;
                justify-items: center;
                gap: 0;
            }

            .org-trunk__spacer {
                display: none;
            }

            .org-trunk__line {
                width: 2px;
                height: 24px;
            }

            .org-trunk__side {
                padding: 0;
                width: 100%;
                align-items: center;
            }

            .org-trunk__side .org-card::before {
                display: none;
            }

            .org-trunk::after {
                content: "";
                width: 2px;
                height: 24px;
                background: var(--org-line);
            }

            .org-branches {
                flex-direction: column;
                gap: 12px;
                margin-left: 12px;
                padding: 12px 0 12px 20px;
                border-left: 2px solid var(--org-line);
            }

            .org-branch {
                padding: 0;
            }

            .org-branch::before {
                display: none;
            }

            .org-branch::after {
                top: 50%;
                left: -20px;
                width: 20px;
                height: 0;
                border-left: 0;
                border-top: 2px solid var(--org-line);
            }

            .org-branch .org-card--stack {
                flex-direction: row;
                text-align: left;
                border-radius: 999px;
                padding: 6px 18px 6px 6px;
            }

            .org-branch__label {
                display: none;
            }

            .org-unit__grid {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            body {
                background: #fff;
            }

            .org-card {
                box-shadow: none;
                border: 1px solid #ccc;
            }

            .org-unit {
                break-inside: avoid;
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

        <nav class="org-version" aria-label="Pilih versi">
            @foreach ($versionLinks as $link)
                <a href="{{ $link['url'] }}" class="{{ $link['active'] ? 'is-active' : '' }}"
                    @if ($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
            @endforeach
        </nav>

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
</body>

</html>
