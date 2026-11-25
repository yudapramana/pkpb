<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bagan Struktur Organisasi - {{ config('organization.title') }}</title>
    <meta name="description" content="Bagan Struktur Organisasi {{ config('organization.title') }}">
    <link href="{{ asset('arsha/assets/img/favicon.png') }}" rel="icon">

    <style>
        :root {
            --t-teal: #24524f;
            --t-line: #4a4a4a;
            --t-border: #9aa5a4;
            --t-muted: #4f5b5a;
            --t-gap: 22px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f1f1f1;
            color: #111;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        /* ---------- Toolbar ---------- */
        .toolbar {
            position: sticky;
            top: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            padding: 10px 16px;
            background: var(--t-teal);
            z-index: 2;
        }

        .toolbar button,
        .toolbar a {
            font: inherit;
            font-size: 14px;
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, .6);
            background: transparent;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }

        .toolbar button.primary {
            background: #fff;
            color: var(--t-teal);
            font-weight: 600;
        }

        .page {
            max-width: 1360px;
            margin: 24px auto;
            padding: 40px 32px 48px;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .12);
        }

        /* ---------- Judul ---------- */
        .t-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .t-header h1 {
            margin: 0;
            font-size: 18px;
            letter-spacing: .02em;
        }

        .t-header h2 {
            margin: 4px 0 8px;
            font-size: 20px;
            color: var(--t-teal);
            text-transform: uppercase;
        }

        .t-header p {
            margin: 0;
            font-size: 12px;
        }

        /* ---------- Kotak ---------- */
        .t-head {
            border: 1.5px solid var(--t-teal);
            border-radius: 4px;
            background: #fff;
            text-align: center;
            overflow: hidden;
        }

        .t-head__unit {
            background: var(--t-teal);
            color: #fff;
            font-weight: 700;
            font-size: 11.5px;
            padding: 6px 8px;
            line-height: 1.3;
        }

        .t-head__body {
            padding: 8px 8px 9px;
        }

        .t-item {
            border: 1px solid var(--t-border);
            border-radius: 4px;
            background: #fff;
            padding: 5px 8px;
        }

        .t-item__name {
            font-weight: 700;
            font-size: 12px;
            line-height: 1.3;
        }

        .t-item__title {
            font-size: 11px;
            line-height: 1.3;
            color: var(--t-muted);
            margin-top: 1px;
        }

        .t-item__note {
            display: inline-block;
            margin-top: 3px;
            padding: 0 6px;
            border: 1px dashed #b07a1a;
            border-radius: 3px;
            color: #7a4b00;
            font-size: 10px;
            font-style: italic;
        }

        .toolbar .versions {
            display: inline-flex;
            gap: 4px;
            padding-left: 8px;
            border-left: 1px solid rgba(255, 255, 255, .35);
        }

        .toolbar .versions a.is-active {
            background: #fdf0d5;
            border-color: #fdf0d5;
            color: var(--t-teal);
            font-weight: 600;
        }

        .t-item__nip {
            font-size: 10.5px;
            margin-top: 2px;
            font-variant-numeric: tabular-nums;
        }

        /* ---------- Puncak + batang + cabang samping ---------- */
        .t-root {
            display: flex;
            justify-content: center;
        }

        .t-root .t-head {
            width: 340px;
        }

        .t-trunk {
            display: grid;
            grid-template-columns: 1fr 2px 1fr;
        }

        .t-trunk__line {
            background: var(--t-line);
        }

        .t-side {
            position: relative;
            padding: 24px 0 28px 40px;
        }

        .t-side>.t-head {
            position: relative;
            max-width: 340px;
        }

        /* garis mendatar dari batang ke kotak Sub Bagian */
        .t-side>.t-head::before {
            content: "";
            position: absolute;
            right: 100%;
            top: 50%;
            width: 40px;
            border-top: 2px solid var(--t-line);
        }

        /* staf Sub Bagian: dua kolom, dikurung garis dari kotak kepala */
        .t-side .t-staff-grid {
            margin: 0 0 0 18px;
            padding: 10px 0 0 14px;
            border-left: 1.5px solid var(--t-line);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }

        /* ---------- Cabang seksi/penyelenggara ---------- */
        .t-branches {
            display: flex;
        }

        .t-branch {
            flex: 1 1 0;
            min-width: 0;
            position: relative;
            padding: var(--t-gap) 5px 0;
        }

        .t-branch::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            border-top: 2px solid var(--t-line);
        }

        .t-branch:first-child::before {
            left: 50%;
        }

        .t-branch:last-child::before {
            right: 50%;
        }

        .t-branch::after {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            height: var(--t-gap);
            border-left: 2px solid var(--t-line);
        }

        /* staf menggantung di bawah kepala dengan garis cabang */
        .t-staff {
            list-style: none;
            margin: 0 0 0 14px;
            padding: 8px 0 0 12px;
        }

        .t-staff li {
            position: relative;
            padding: 3px 0;
        }

        .t-staff li::before {
            content: "";
            position: absolute;
            left: -12px;
            top: 0;
            bottom: 0;
            border-left: 1.5px solid var(--t-line);
        }

        .t-staff li:first-child::before {
            top: -8px;
        }

        .t-staff li:last-child::before {
            bottom: auto;
            height: 22px;
        }

        .t-staff li:first-child:last-child::before {
            height: 30px;
        }

        .t-staff li::after {
            content: "";
            position: absolute;
            left: -12px;
            top: 22px;
            width: 12px;
            border-top: 1.5px solid var(--t-line);
        }

        /* ---------- Kelompok jabatan tanpa kepala (mis. Kelompok Jabatan Pengawas) ---------- */
        .t-functional {
            margin-top: 40px;
            border: 1.5px dashed var(--t-teal);
            border-radius: 6px;
            padding: 0 14px 14px;
        }

        .t-functional h3 {
            display: inline-block;
            margin: -10px 0 10px;
            padding: 0 8px;
            background: #fff;
            color: var(--t-teal);
            font-size: 13px;
            transform: translateY(-1px);
        }

        .t-functional .t-staff-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 6px;
        }

        .t-empty {
            text-align: center;
            color: var(--t-muted);
            padding: 40px 0;
        }

        /* ---------- Layar kecil: susun ke bawah ---------- */
        @media (max-width: 991.98px) {
            .page {
                margin: 0;
                padding: 24px 12px;
            }

            .t-root .t-head {
                width: 100%;
            }

            .t-trunk {
                grid-template-columns: 1fr;
            }

            .t-trunk__spacer,
            .t-trunk__line {
                display: none;
            }

            .t-side {
                padding: 16px 0 0;
            }

            .t-side>.t-head {
                max-width: none;
            }

            .t-side>.t-head::before {
                display: none;
            }

            .t-side .t-staff-grid {
                grid-template-columns: 1fr;
            }

            .t-branches {
                flex-direction: column;
                gap: 16px;
                margin-top: 16px;
            }

            .t-branch {
                padding: 0;
            }

            .t-branch::before,
            .t-branch::after {
                display: none;
            }
        }

        /* ---------- Cetak: lanskap, tanpa toolbar ---------- */
        @page {
            size: A3 landscape;
            margin: 12mm;
        }

        @media print {
            body {
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .toolbar {
                display: none;
            }

            .page {
                margin: 0;
                padding: 0;
                box-shadow: none;
                max-width: none;
            }

            .t-item,
            .t-head,
            .t-staff li {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button type="button" class="primary" onclick="window.print()">Cetak</button>
        <a href="{{ route('organization.index', $versionQuery) }}">Bagan dengan foto</a>
        <a href="{{ route('organization.simple', $versionQuery) }}">Versi tabel (Word)</a>
        <span class="versions" role="group" aria-label="Pilih versi">
            @foreach ($versionLinks as $link)
                <a href="{{ $link['url'] }}" class="{{ $link['active'] ? 'is-active' : '' }}"
                    @if ($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
            @endforeach
        </span>
    </div>

    <main class="page">
        <header class="t-header">
            <h1>STRUKTUR ORGANISASI</h1>
            <h2>{{ config('organization.title') }}</h2>
            @if (config('organization.legal_basis'))
                <p>{{ config('organization.legal_basis') }}</p>
            @endif
            @if ($asOf)
                <p>Per {{ $asOf }}</p>
            @endif
            <p><strong>Versi: {{ $versionLabel }}</strong></p>
        </header>

        @if (!$root)
            <p class="t-empty">Data struktur organisasi belum tersedia.</p>
        @else
            {{-- Puncak --}}
            <div class="t-root">
                @include('organization._tree-head', ['unit' => $root])
            </div>

            {{-- Batang + Sub Bagian di samping beserta stafnya --}}
            @if ($sideUnits->isNotEmpty() || $branchUnits->isNotEmpty())
                <div class="t-trunk">
                    <span class="t-trunk__spacer"></span>
                    <span class="t-trunk__line"></span>
                    <div>
                        @foreach ($sideUnits as $unit)
                            <div class="t-side">
                                @include('organization._tree-head', ['unit' => $unit])
                                @if ($unit->staffPositions->isNotEmpty())
                                    <div class="t-staff-grid">
                                        @foreach ($unit->staffPositions as $position)
                                            @include('organization._tree-item', ['position' => $position])
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Cabang seksi/penyelenggara, staf menggantung di bawah kepala --}}
            @if ($branchUnits->isNotEmpty())
                <div class="t-branches">
                    @foreach ($branchUnits as $unit)
                        <div class="t-branch">
                            @include('organization._tree-head', ['unit' => $unit])
                            @if ($unit->staffPositions->isNotEmpty())
                                <ul class="t-staff">
                                    @foreach ($unit->staffPositions as $position)
                                        <li>@include('organization._tree-item', ['position' => $position])</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Kelompok jabatan tanpa kepala (mis. Kelompok Jabatan Pengawas) --}}
            @foreach ($functionalUnits as $unit)
                @continue($unit->staffPositions->isEmpty())
                <section class="t-functional">
                    <h3>{{ $unit->name }}</h3>
                    <div class="t-staff-grid">
                        @foreach ($unit->staffPositions as $position)
                            @include('organization._tree-item', ['position' => $position])
                        @endforeach
                    </div>
                </section>
            @endforeach
        @endif
    </main>
</body>

</html>
