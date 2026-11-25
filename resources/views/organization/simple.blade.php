<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Organisasi (Teks) - {{ config('organization.title') }}</title>
    <meta name="description" content="Struktur Organisasi {{ config('organization.title') }} dalam format teks">
    <link href="{{ asset('arsha/assets/img/favicon.png') }}" rel="icon">

    {{-- Gaya halaman saja. Gaya dokumen ditulis inline agar ikut terbawa saat disalin ke Word. --}}
    <style>
        body {
            margin: 0;
            background: #f1f1f1;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
        }

        .toolbar {
            position: sticky;
            top: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            background: #24524f;
            color: #fff;
            font-size: 14px;
            z-index: 1;
        }

        .toolbar button,
        .toolbar a {
            font: inherit;
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
            color: #24524f;
            border-color: #fff;
            font-weight: 600;
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
            color: #24524f;
            font-weight: 600;
        }

        .toolbar .status {
            min-width: 160px;
            font-size: 13px;
        }

        .page {
            max-width: 900px;
            margin: 24px auto;
            padding: 48px 56px;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .12);
        }

        @media (max-width: 700px) {
            .page {
                margin: 0;
                padding: 24px 16px;
            }
        }

        @media print {
            body {
                background: #fff;
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

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button type="button" class="primary" id="copy-doc">Salin semua (untuk Word)</button>
        <button type="button" onclick="window.print()">Cetak</button>
        <a href="{{ route('organization.index', $versionQuery) }}">Bagan dengan foto</a>
        <a href="{{ route('organization.tree', $versionQuery) }}">Bagan tanpa foto</a>
        <span class="versions" role="group" aria-label="Pilih versi">
            @foreach ($versionLinks as $link)
                <a href="{{ $link['url'] }}" class="{{ $link['active'] ? 'is-active' : '' }}"
                    @if ($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
            @endforeach
        </span>
        <span class="status" id="copy-status" role="status" aria-live="polite"></span>
    </div>

    @php
        $font = 'font-family:Arial,Helvetica,sans-serif;';
        $pCenter = $font . 'text-align:center;margin:0;';
        $heading = $font . 'font-size:12pt;font-weight:bold;margin:18pt 0 6pt;';
        $table = $font . 'border-collapse:collapse;width:100%;font-size:11pt;';
        $th = 'border:1px solid #000;padding:4pt 6pt;background:#d9d9d9;font-weight:bold;text-align:center;vertical-align:middle;';
        $td = 'border:1px solid #000;padding:4pt 6pt;vertical-align:top;';
        $letters = range('A', 'Z');
        $sectionNo = 0;
    @endphp

    <main class="page">
        <div id="doc">
            <p style="{{ $pCenter }}font-size:14pt;font-weight:bold;">STRUKTUR ORGANISASI</p>
            <p style="{{ $pCenter }}font-size:14pt;font-weight:bold;text-transform:uppercase;">{{ config('organization.title') }}</p>
            @if (config('organization.legal_basis'))
                <p style="{{ $pCenter }}font-size:10pt;margin-top:6pt;">{{ config('organization.legal_basis') }}</p>
            @endif
            @if ($asOf)
                <p style="{{ $pCenter }}font-size:10pt;">Per {{ $asOf }}</p>
            @endif
            <p style="{{ $pCenter }}font-size:10pt;font-weight:bold;">Versi: {{ $versionLabel }}</p>

            @if (!$root)
                <p style="{{ $font }}margin-top:18pt;">Data struktur organisasi belum tersedia.</p>
            @endif

            @foreach ($units as $unit)
                @php
                    $rows = $unit->headPosition ? collect([$unit->headPosition])->concat($unit->staffPositions) : $unit->staffPositions;
                @endphp
                @continue($rows->isEmpty())

                <p style="{{ $heading }}">{{ $letters[$sectionNo++] ?? $sectionNo }}. {{ $unit->name }}</p>

                <table style="{{ $table }}" cellpadding="0" cellspacing="0" border="1">
                    <thead>
                        <tr>
                            <th style="{{ $th }}width:5%;">No</th>
                            <th style="{{ $th }}width:27%;">Nama</th>
                            <th style="{{ $th }}width:20%;">NIP</th>
                            <th style="{{ $th }}width:30%;">Jabatan</th>
                            <th style="{{ $th }}width:18%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $i => $position)
                            <tr>
                                <td style="{{ $td }}text-align:center;">{{ $i + 1 }}</td>
                                <td style="{{ $td }}{{ $position->is_head ? 'font-weight:bold;' : '' }}">{{ $position->employee->name }}</td>
                                <td style="{{ $td }}white-space:nowrap;">{{ $position->employee->nip ?: '-' }}</td>
                                <td style="{{ $td }}">{{ $position->title }}</td>
                                <td style="{{ $td }}font-size:9pt;">{{ $position->versionNote($version) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>
    </main>

    <script>
        (function () {
            var button = document.getElementById('copy-doc');
            var status = document.getElementById('copy-status');

            // Salin sebagai teks berformat (tabel tetap utuh saat di-paste ke Word).
            button.addEventListener('click', function () {
                var selection = window.getSelection();
                var range = document.createRange();
                range.selectNodeContents(document.getElementById('doc'));
                selection.removeAllRanges();
                selection.addRange(range);

                var ok = false;
                try {
                    ok = document.execCommand('copy');
                } catch (e) {
                    ok = false;
                }
                selection.removeAllRanges();

                status.textContent = ok
                    ? 'Tersalin. Tempel (Ctrl+V) di Word.'
                    : 'Gagal menyalin otomatis. Blok isi halaman lalu tekan Ctrl+C.';
                setTimeout(function () { status.textContent = ''; }, 4000);
            });
        })();
    </script>
</body>

</html>
