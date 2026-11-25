<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Portal Kemenag Kab Pesisir Selatan</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="Portal Kemenag Kab Pesisir Selatan">
    <meta name="description" content="Portal Web Kementerian Agama Kabupaten Pesisir Selatan">
    <meta name="keywords" content="Web Kemenag Pesisir Selatan, Portal Web, KemenagPessel, Kementerian Agama Kabupaten Pesisir Selatan">
    <meta name="author" content="Pramana Yuda Sayeti" />
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
    <meta name="revisit-after" content="1 Days" />
    <meta name="theme-color" content="#0f3b33">

    <!-- Open Graph / Facebook -->
    <meta property="og:site_name" content="Web Portal Kementerian Agama Kabupaten Pesisir Selatan">
    <meta property="og:title" content="Web Portal Kementerian Agama Kabupaten Pesisir Selatan ">
    <meta property="og:locale" content="id_ID">

    <meta property="og:type" content=article />
    <meta property="og:url" content="{{ URL::current() }}">
    <meta property="og:description" content="Portal Web Kementerian Agama Kabupaten Pesisir Selatan">
    <meta property="og:image" content="">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ URL::current() }}">
    <meta property="twitter:title" content="Portal Web Kementerian Agama Kabupaten Pesisir Selatan">
    <meta property="twitter:description" content="Portal Web Kementerian Agama Kabupaten Pesisir Selatan">
    <meta property="twitter:image" content="">
    <meta name="twitter:site" content="{{ URL::current() }}" />

    <link rel="canonical" href="{{ URL::current() }}" />
    <link rel="alternate" hreflang="en-US" href="{{ URL::current() }}" />

    <!-- Favicons -->
    <link href="{{ asset('arsha/assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('arsha/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Poppins:wght@400;500;600;700&family=Jost:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('arsha/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('arsha/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <script src="https://cdn.userway.org/widget.js" data-account="F8ts5ygdnQ"></script>

    @php
        /*
         * Pola geometri Islam (bintang segi delapan) dibuat sebagai SVG data URI,
         * dipakai sebagai latar halaman (krem) dan latar kepala halaman (hijau tua).
         */
        $star = function ($cx, $cy, $outer, $inner) {
            $points = [];
            for ($k = 0; $k < 16; $k++) {
                $angle = deg2rad(-90 + $k * 22.5);
                $radius = $k % 2 ? $inner : $outer;
                $points[] = round($cx + $radius * cos($angle), 2) . ',' . round($cy + $radius * sin($angle), 2);
            }
            return implode(' ', $points);
        };
        $pattern = function ($stroke, $opacity) use ($star) {
            $big = $star(48, 48, 20, 15.3);
            $inner = $star(48, 48, 9, 6.9);
            $corners = '';
            foreach ([[0, 0], [96, 0], [0, 96], [96, 96]] as $c) {
                $corners .= '<polygon points="' . $star($c[0], $c[1], 10, 7.65) . '"/>';
            }
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96">'
                . '<g fill="none" stroke="' . $stroke . '" stroke-opacity="' . $opacity . '" stroke-width="1.1">'
                . '<polygon points="' . $big . '"/><polygon points="' . $inner . '"/>' . $corners
                . '<path d="M48 0V28M48 68V96M0 48H28M68 48H96M7.1 7.1L33.9 33.9M88.9 7.1L62.1 33.9M7.1 88.9L33.9 62.1M88.9 88.9L62.1 62.1"/>'
                . '<path d="M48 0L52 4L48 8L44 4Z M48 88L52 92L48 96L44 92Z M0 48L4 44L8 48L4 52Z M88 48L92 44L96 48L92 52Z"/>'
                . '</g></svg>';
            return 'url("data:image/svg+xml,' . rawurlencode($svg) . '")';
        };
        $patternLight = $pattern('#24524f', .09);
        $patternDark = $pattern('#e2bd62', .22);

        // Arah kiblat (azimut dari utara searah jarum jam) dan jarak ke Ka'bah.
        $loc = config('portal.location');
        $kaaba = config('portal.kaaba');
        $phi1 = deg2rad($loc['latitude']);
        $phi2 = deg2rad($kaaba['latitude']);
        $dLambda = deg2rad($kaaba['longitude'] - $loc['longitude']);
        $qibla = fmod(rad2deg(atan2(sin($dLambda), cos($phi1) * tan($phi2) - sin($phi1) * cos($dLambda))) + 360, 360);
        $haversine = sin(($phi2 - $phi1) / 2) ** 2 + cos($phi1) * cos($phi2) * sin($dLambda / 2) ** 2;
        $kaabaDistance = 6371 * 2 * atan2(sqrt($haversine), sqrt(1 - $haversine));
        $directions = ['Utara', 'Timur Laut', 'Timur', 'Tenggara', 'Selatan', 'Barat Daya', 'Barat', 'Barat Laut'];
        $qiblaDirection = $directions[(int) round($qibla / 45) % 8];

        $now = now($loc['timezone'])->locale('id');
        $clientConfig = ['location' => $loc, 'prayer' => config('portal.prayer')];

        $tagline = 'Dalam rangka mempertahankan predikat pembangunan ZI-WBBM maka seluruh jajaran Kankemenag Kab Pesisir Selatan berkomitmen untuk senantiasa menjaga integritas dan memberi pelayanan prima dengan tagline';
        $taglineBold = 'PESONA DILAN (Profesional, Efektif, Sistematis, Optimal, Nol Rupiah, Amanah dan Berkeadilan).';
    @endphp

    {{-- Palet: hijau tua & emas khas Kementerian Agama, krem dari halaman Struktur Organisasi. --}}
    <style>
        :root {
            --p-bg: #efe9db;
            --p-panel: #f7f3ea;
            --p-green-900: #0b2f29;
            --p-green-800: #0f3b33;
            --p-teal: #24524f;
            --p-teal-dark: #1b3f3d;
            --p-teal-soft: #3d726e;
            --p-emerald: #1d6b4f;
            --p-gold: #d8a93b;
            --p-gold-soft: #e9c977;
            --p-cream: #fdf0d5;
            --p-text: #1f2a2a;
            --p-muted: #5f6b6a;
            --ticker-h: 40px;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: var(--ticker-h);
            color: var(--p-text);
            font-family: "Poppins", sans-serif;
            font-size: 14px;
            background-color: var(--p-bg);
            background-image:
                {!! $patternLight !!},
                radial-gradient(ellipse at 0% 40%, rgba(29, 107, 79, .10), transparent 55%),
                radial-gradient(ellipse at 100% 90%, rgba(216, 169, 59, .16), transparent 50%);
            background-size: 96px 96px, auto, auto;
            background-attachment: fixed;
        }

        /* ---------- Tagline berjalan ---------- */
        .ticker {
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 1000;
            display: flex;
            align-items: stretch;
            height: var(--ticker-h);
            background: var(--p-green-900);
            color: var(--p-cream);
            border-bottom: 2px solid var(--p-gold);
            overflow: hidden;
        }

        .ticker__label {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 0 18px 0 16px;
            background: var(--p-gold);
            color: var(--p-green-900);
            font-weight: 700;
            font-size: .8rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            white-space: nowrap;
            clip-path: polygon(0 0, 100% 0, calc(100% - 10px) 100%, 0 100%);
        }

        .ticker__viewport {
            flex: 1;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .ticker__track {
            display: flex;
            width: max-content;
            animation: ticker 45s linear infinite;
        }

        .ticker:hover .ticker__track {
            animation-play-state: paused;
        }

        .ticker__item {
            padding-right: 6rem;
            white-space: nowrap;
            font-size: .9rem;
        }

        .ticker__item b {
            color: var(--p-gold-soft);
        }

        @keyframes ticker {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        /* ---------- Kepala halaman (hijau tua + pola emas) ---------- */
        .hero {
            position: relative;
            overflow: hidden;
            color: var(--p-cream);
            text-align: center;
            padding: 36px 16px 150px;
            background-color: var(--p-green-800);
            background-image:
                radial-gradient(ellipse at 50% 0%, rgba(233, 201, 119, .28), transparent 60%),
                {!! $patternDark !!},
                linear-gradient(160deg, var(--p-green-900) 0%, var(--p-teal) 55%, var(--p-emerald) 100%);
            background-size: auto, 96px 96px, auto;
        }

        /* Tepi bawah berbentuk deretan lengkung */
        .hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 22px;
            background: radial-gradient(circle at 22px 22px, var(--p-bg) 21px, transparent 22px) repeat-x;
            background-size: 44px 22px;
        }

        .hero__inner {
            position: relative;
            z-index: 1;
            max-width: 880px;
            margin: 0 auto;
        }

        /* Mihrab: bingkai lengkung runcing di belakang logo */
        .hero__mihrab {
            position: relative;
            width: 150px;
            margin: 0 auto;
            padding-top: 26px;
        }

        .hero__mihrab svg {
            position: absolute;
            inset: 0 0 auto 0;
            width: 150px;
            height: 150px;
        }

        .hero__logo {
            position: relative;
            width: 88px;
            height: auto;
            filter: drop-shadow(0 6px 14px rgba(0, 0, 0, .35));
        }

        .hero__bismillah {
            font-family: "Amiri", serif;
            font-size: clamp(1.4rem, 3.4vw, 1.9rem);
            color: var(--p-gold-soft);
            margin: 22px 0 0;
            line-height: 1.4;
        }

        .hero__title {
            font-family: "Jost", sans-serif;
            font-weight: 800;
            font-size: clamp(2.4rem, 7vw, 4.2rem);
            line-height: 1;
            letter-spacing: .04em;
            margin: 6px 0 12px;
            background: linear-gradient(180deg, #fff6dc 10%, var(--p-gold-soft) 60%, var(--p-gold) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 2px 0 rgba(0, 0, 0, .05);
        }

        .hero__office {
            font-family: "Jost", sans-serif;
            font-weight: 600;
            font-size: clamp(.95rem, 2.3vw, 1.3rem);
            letter-spacing: .06em;
            text-transform: uppercase;
            margin: 0;
            color: #fff;
        }

        .ornament {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 14px auto;
            color: var(--p-gold);
        }

        .ornament::before,
        .ornament::after {
            content: "";
            width: 70px;
            height: 1px;
            background: linear-gradient(90deg, transparent, currentColor);
        }

        .ornament::after {
            transform: scaleX(-1);
        }

        .ornament svg {
            width: 22px;
            height: 22px;
        }

        .hero__acronym {
            margin: 0;
            font-size: .82rem;
            letter-spacing: .14em;
            color: rgba(253, 240, 213, .75);
            text-transform: uppercase;
        }

        .hero__acronym b {
            color: var(--p-gold-soft);
            font-weight: 700;
        }

        /* Lentera gantung */
        .lantern {
            position: absolute;
            top: 0;
            width: 58px;
            transform-origin: 50% 0;
            animation: sway 6s ease-in-out infinite;
            filter: drop-shadow(0 0 18px rgba(233, 201, 119, .45));
        }

        .lantern--1 {
            left: 7%;
        }

        .lantern--2 {
            left: 15%;
            width: 44px;
            animation-delay: -2s;
        }

        .lantern--3 {
            right: 8%;
            animation-delay: -4s;
        }

        .lantern--4 {
            right: 16%;
            width: 44px;
            animation-delay: -1s;
        }

        @keyframes sway {

            0%,
            100% {
                transform: rotate(2.5deg);
            }

            50% {
                transform: rotate(-2.5deg);
            }
        }

        /* ---------- Isi ---------- */
        .page {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1180px;
            margin: -118px auto 0;
            padding: 0 16px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 18px;
            font-family: "Jost", sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--p-teal-dark);
            letter-spacing: .02em;
        }

        .section-title svg {
            width: 22px;
            height: 22px;
            color: var(--p-gold);
            flex: none;
        }

        .section-title::after {
            content: "";
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(36, 82, 79, .35), transparent);
        }

        /* ---------- Waktu & ibadah ---------- */
        .worship {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 2fr) minmax(0, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        .w-card {
            position: relative;
            border-radius: 22px;
            background: #fff;
            border: 1px solid rgba(36, 82, 79, .12);
            box-shadow: 0 14px 34px rgba(11, 47, 41, .12);
            padding: 22px;
            overflow: hidden;
        }

        .w-card__label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 12px;
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--p-teal-soft);
        }

        /* Jam */
        .w-clock {
            color: var(--p-cream);
            background-color: var(--p-green-800);
            background-image: {!! $patternDark !!}, linear-gradient(160deg, var(--p-teal) 0%, var(--p-green-900) 100%);
            background-size: 96px 96px, auto;
            border: 1px solid rgba(216, 169, 59, .45);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .w-clock .w-card__label {
            color: var(--p-gold-soft);
        }

        .w-clock__time {
            font-family: "Jost", sans-serif;
            font-weight: 700;
            font-size: clamp(2.3rem, 3.6vw, 2.9rem);
            line-height: 1;
            white-space: nowrap;
            color: #fff;
            font-variant-numeric: tabular-nums;
            letter-spacing: .02em;
        }

        .w-clock__sec {
            font-size: .45em;
            color: var(--p-gold-soft);
            margin-left: 2px;
        }

        .w-clock__tz {
            font-family: "Poppins", sans-serif;
            font-size: .7rem;
            font-weight: 600;
            margin-left: 8px;
            padding: 2px 8px;
            border-radius: 999px;
            border: 1px solid rgba(233, 201, 119, .6);
            color: var(--p-gold-soft);
            vertical-align: middle;
        }

        .w-clock__date {
            margin: 14px 0 0;
            font-weight: 600;
            font-size: .95rem;
            color: #fff;
        }

        .w-clock__hijri {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 10px 0 0;
            padding-top: 10px;
            border-top: 1px dashed rgba(233, 201, 119, .45);
            color: var(--p-gold-soft);
            font-weight: 600;
        }

        .w-clock__hijri small {
            display: block;
            font-weight: 400;
            font-size: .7rem;
            color: rgba(253, 240, 213, .65);
        }

        /* Jadwal shalat */
        .w-prayer {
            display: flex;
            flex-direction: column;
        }

        .w-prayer__head {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .w-prayer__head .w-card__label {
            margin: 0;
        }

        .w-next {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(216, 169, 59, .16);
            color: #7a5410;
            font-size: .8rem;
            font-weight: 600;
        }

        .w-next b {
            font-variant-numeric: tabular-nums;
        }

        .w-times {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 8px;
            flex: 1;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .w-time {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            padding: 12px 4px 10px;
            border-radius: 14px;
            background: var(--p-panel);
            border: 1px solid rgba(36, 82, 79, .08);
        }

        .w-time i {
            display: block;
            font-size: 1.05rem;
            color: var(--p-teal-soft);
        }

        .w-time__name {
            display: block;
            margin-top: 2px;
            font-size: .72rem;
            color: var(--p-muted);
        }

        .w-time__at {
            display: block;
            font-family: "Jost", sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--p-teal-dark);
            font-variant-numeric: tabular-nums;
        }

        .w-time--minor .w-time__at {
            color: var(--p-muted);
            font-weight: 600;
        }

        .w-time.is-next {
            background: linear-gradient(160deg, var(--p-teal) 0%, var(--p-green-900) 100%);
            border-color: var(--p-gold);
            box-shadow: 0 8px 18px rgba(11, 47, 41, .25);
        }

        .w-time.is-next i,
        .w-time.is-next .w-time__name {
            color: var(--p-gold-soft);
        }

        .w-time.is-next .w-time__at {
            color: #fff;
        }

        .w-note {
            margin: 12px 0 0;
            font-size: .7rem;
            color: var(--p-muted);
        }

        /* Kiblat */
        .w-qibla {
            text-align: center;
        }

        .w-qibla .w-card__label {
            justify-content: center;
        }

        .compass {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            display: block;
        }

        .w-qibla__deg {
            margin: 10px 0 0;
            font-family: "Jost", sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            line-height: 1.1;
            color: var(--p-teal-dark);
        }

        .w-qibla__deg small {
            display: block;
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-size: .72rem;
            color: var(--p-muted);
        }

        /* ---------- Daftar layanan ---------- */
        .portal__panel {
            position: relative;
            background: rgba(247, 243, 234, .92);
            border: 1px solid rgba(36, 82, 79, .12);
            border-radius: 26px;
            padding: clamp(18px, 3vw, 32px);
            box-shadow: 0 10px 30px rgba(36, 82, 79, .08);
        }

        /* Sudut hiasan emas */
        .portal__panel::before,
        .portal__panel::after {
            content: "";
            position: absolute;
            width: 46px;
            height: 46px;
            border: 2px solid var(--p-gold);
            opacity: .7;
            pointer-events: none;
        }

        .portal__panel::before {
            top: 10px;
            left: 10px;
            border-right: 0;
            border-bottom: 0;
            border-top-left-radius: 18px;
        }

        .portal__panel::after {
            right: 10px;
            bottom: 10px;
            border-left: 0;
            border-top: 0;
            border-bottom-right-radius: 18px;
        }

        .portal__grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 16px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .portal-card {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 14px;
            width: 100%;
            height: 100%;
            padding: 22px 12px 18px;
            border: 1px solid rgba(36, 82, 79, .12);
            border-radius: 18px;
            background: #fff;
            color: var(--p-text);
            text-align: center;
            text-decoration: none;
            font: inherit;
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
            animation: rise .5s ease both;
            animation-delay: calc(var(--i, 0) * 30ms);
        }

        /* Garis emas tipis di atas kartu saat disorot */
        .portal-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 20%;
            right: 20%;
            height: 3px;
            border-radius: 0 0 3px 3px;
            background: var(--p-gold);
            transform: scaleX(0);
            transition: transform .25s ease;
        }

        .portal-card__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 92px;
            height: 92px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(36, 82, 79, .08), rgba(36, 82, 79, 0) 70%);
        }

        .portal-card__icon img {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }

        .portal-card__title {
            font-weight: 600;
            font-size: .9rem;
            line-height: 1.3;
            color: var(--p-teal-dark);
        }

        .portal-card__hint {
            position: absolute;
            top: 10px;
            right: 10px;
            color: var(--p-teal-soft);
            font-size: .85rem;
            opacity: 0;
            transition: opacity .2s ease;
        }

        a.portal-card:hover,
        a.portal-card:focus-visible,
        button.portal-card:hover,
        button.portal-card:focus-visible {
            transform: translateY(-4px);
            border-color: var(--p-teal);
            box-shadow: 0 12px 24px rgba(36, 82, 79, .16);
            outline: none;
        }

        a.portal-card:hover::before,
        a.portal-card:focus-visible::before,
        button.portal-card:hover::before,
        button.portal-card:focus-visible::before {
            transform: scaleX(1);
        }

        a.portal-card:hover .portal-card__hint,
        a.portal-card:focus-visible .portal-card__hint,
        button.portal-card:hover .portal-card__hint,
        button.portal-card:focus-visible .portal-card__hint {
            opacity: 1;
        }

        .portal-card--soon {
            cursor: default;
            background: rgba(255, 255, 255, .6);
        }

        .portal-card--soon .portal-card__icon img {
            filter: grayscale(.35);
            opacity: .8;
        }

        .portal-card__badge {
            margin-top: -6px;
            padding: 1px 10px;
            border-radius: 999px;
            background: rgba(36, 82, 79, .08);
            color: var(--p-muted);
            font-size: .7rem;
            font-weight: 500;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .portal__empty {
            text-align: center;
            color: var(--p-muted);
            padding: 24px 0;
            margin: 0;
        }

        /* ---------- Tautan struktur organisasi ---------- */
        .org-link {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
            margin-top: 20px;
            padding: 18px 22px;
            border-radius: 20px;
            color: var(--p-cream);
            text-decoration: none;
            border: 1px solid rgba(216, 169, 59, .5);
            background-color: var(--p-green-800);
            background-image: {!! $patternDark !!}, linear-gradient(120deg, var(--p-green-900), var(--p-teal));
            background-size: 96px 96px, auto;
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .org-link:hover,
        .org-link:focus-visible {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(11, 47, 41, .3);
        }

        .org-link__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(233, 201, 119, .16);
            color: var(--p-gold-soft);
            font-size: 1.5rem;
            flex: none;
        }

        .org-link__text {
            flex: 1;
            min-width: 200px;
        }

        .org-link__text strong {
            display: block;
            font-family: "Jost", sans-serif;
            font-size: 1.1rem;
            color: #fff;
        }

        .org-link__text span {
            font-size: .82rem;
            opacity: .85;
        }

        .org-link__cta {
            padding: 8px 18px;
            border-radius: 999px;
            background: var(--p-gold);
            color: var(--p-green-900);
            font-weight: 600;
            font-size: .85rem;
            white-space: nowrap;
        }

        /* ---------- Kaki halaman ---------- */
        .site-footer {
            margin-top: auto;
            padding: 40px 16px 28px;
            text-align: center;
        }

        .site-footer .ornament {
            margin: 0 auto 10px;
        }

        .site-footer h5 {
            font-family: "Jost", sans-serif;
            font-weight: 700;
            font-size: .95rem;
            letter-spacing: .1em;
            color: var(--p-teal);
            margin: 0 0 2px;
        }

        .site-footer p {
            margin: 0;
            font-size: .75rem;
            color: var(--p-muted);
        }

        /* ---------- Pop-up layanan ---------- */
        .portal-modal .modal-content {
            border-radius: 18px;
            overflow: hidden;
        }

        .portal-modal .modal-header {
            background: var(--p-teal);
            color: #fff;
        }

        .portal-modal iframe {
            display: block;
            width: 100%;
            height: 75vh;
            border: 0;
            background: var(--p-panel);
        }

        /* ---------- Responsif ---------- */
        @media (max-width: 991.98px) {
            .worship {
                grid-template-columns: 1fr 1fr;
            }

            .w-prayer {
                grid-column: 1 / -1;
                order: 3;
            }

            .lantern--2,
            .lantern--4 {
                display: none;
            }
        }

        @media (max-width: 767.98px) {
            .w-times {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .lantern {
                width: 40px;
            }

            .lantern--1 {
                left: 3%;
            }

            .lantern--3 {
                right: 3%;
            }
        }

        @media (max-width: 575.98px) {
            .worship {
                grid-template-columns: 1fr;
            }

            .w-prayer {
                order: 0;
            }

            .portal__grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .portal-card__icon {
                width: 76px;
                height: 76px;
            }

            .portal-card__icon img {
                max-width: 64px;
                max-height: 64px;
            }

            .ticker__label span {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            .ticker__track,
            .portal-card,
            .lantern {
                animation: none;
            }

            .portal-card,
            .portal-card::before {
                transition: none;
            }
        }
    </style>
</head>

<body>
    {{-- Simbol yang dipakai ulang: bintang segi delapan dan lentera --}}
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <defs>
            <symbol id="i-star8" viewBox="0 0 24 24">
                <path fill="currentColor"
                    d="M12 1.5l2.6 4.2 4.8-1.1-1.1 4.8 4.2 2.6-4.2 2.6 1.1 4.8-4.8-1.1L12 22.5l-2.6-4.2-4.8 1.1 1.1-4.8L1.5 12l4.2-2.6-1.1-4.8 4.8 1.1z" />
                <circle cx="12" cy="12" r="3.2" fill="#fff" fill-opacity=".85" />
            </symbol>
            <symbol id="i-lantern" viewBox="0 0 60 170">
                <line x1="30" y1="0" x2="30" y2="46" stroke="#e9c977" stroke-width="1.5" />
                <circle cx="30" cy="48" r="3" fill="none" stroke="#e9c977" stroke-width="1.5" />
                <path d="M30 51 C 20 54, 14 60, 13 66 H47 C46 60, 40 54, 30 51Z" fill="#d8a93b" />
                <rect x="11" y="66" width="38" height="4" rx="1.5" fill="#b8862a" />
                <path d="M13 70 H47 L43 112 H17Z" fill="#e9c977" fill-opacity=".2" stroke="#d8a93b" stroke-width="1.6" />
                <path d="M30 70 V112 M21.5 70 L23 112 M38.5 70 L37 112" stroke="#d8a93b" stroke-width="1" />
                <ellipse cx="30" cy="92" rx="7" ry="11" fill="#fff3c4" fill-opacity=".9" />
                <rect x="15" y="112" width="30" height="4" rx="1.5" fill="#b8862a" />
                <path d="M19 116 H41 L34 128 H26Z" fill="#d8a93b" />
                <line x1="30" y1="128" x2="30" y2="146" stroke="#e9c977" stroke-width="1.5" />
                <path d="M30 146 l4 8 -4 12 -4 -12z" fill="#d8a93b" />
            </symbol>
        </defs>
    </svg>

    <div class="ticker" role="region" aria-label="Tagline">
        <div class="ticker__label"><i class="bi bi-megaphone-fill" aria-hidden="true"></i> <span>Tagline</span></div>
        <div class="ticker__viewport">
            <div class="ticker__track">
                <div class="ticker__item">{{ $tagline }} <b>{{ $taglineBold }}</b></div>
                {{-- Salinan kedua agar teks berjalan tanpa jeda --}}
                <div class="ticker__item" aria-hidden="true">{{ $tagline }} <b>{{ $taglineBold }}</b></div>
            </div>
        </div>
    </div>

    <header class="hero">
        <svg class="lantern lantern--1" viewBox="0 0 60 170" aria-hidden="true"><use href="#i-lantern" /></svg>
        <svg class="lantern lantern--2" viewBox="0 0 60 170" aria-hidden="true" style="top:-30px"><use href="#i-lantern" /></svg>
        <svg class="lantern lantern--3" viewBox="0 0 60 170" aria-hidden="true"><use href="#i-lantern" /></svg>
        <svg class="lantern lantern--4" viewBox="0 0 60 170" aria-hidden="true" style="top:-30px"><use href="#i-lantern" /></svg>

        <div class="hero__inner">
            <div class="hero__mihrab">
                <svg viewBox="0 0 150 150" aria-hidden="true">
                    <defs>
                        <linearGradient id="mihrab-fill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0" stop-color="#e9c977" stop-opacity=".22" />
                            <stop offset="1" stop-color="#e9c977" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    <path d="M15 150 V78 C15 40, 48 18, 75 4 C102 18, 135 40, 135 78 V150" fill="url(#mihrab-fill)"
                        stroke="#d8a93b" stroke-width="2" />
                    <path d="M27 150 V82 C27 52, 54 32, 75 20 C96 32, 123 52, 123 82 V150" fill="none" stroke="#d8a93b"
                        stroke-opacity=".45" stroke-width="1" />
                    <use href="#i-star8" x="67" y="-6" width="16" height="16" style="color:#e9c977" />
                </svg>
                <img class="hero__logo" src="{{ asset('arsha/assets/img/logokmg2.png') }}" alt="Logo Kementerian Agama">
            </div>

            <p class="hero__bismillah" lang="ar" dir="rtl">بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ</p>
            <h1 class="hero__title">SIMPUL SEHATI</h1>
            <p class="hero__office">Kantor Kementerian Agama Kabupaten Pesisir Selatan</p>
            <div class="ornament" aria-hidden="true"><svg><use href="#i-star8" /></svg></div>
            <p class="hero__acronym">
                <b>SI</b>stem <b>M</b>anajemen <b>P</b>elayanan <b>PU</b>b<b>L</b>ik <b>SE</b>penuh <b>HATI</b>
            </p>
        </div>
    </header>

    <main class="page">
        {{-- ================= Waktu, Hijriah, Jadwal Shalat & Kiblat ================= --}}
        <section class="worship" aria-label="Waktu dan jadwal ibadah">
            <div class="w-card w-clock">
                <p class="w-card__label"><i class="bi bi-clock-fill" aria-hidden="true"></i> Waktu {{ $loc['name'] }}</p>
                <div class="w-clock__time">
                    <span id="clock-hm">{{ $now->format('H:i') }}</span><span class="w-clock__sec" id="clock-s">{{ $now->format(':s') }}</span><span
                        class="w-clock__tz">{{ $loc['timezone_label'] }}</span>
                </div>
                <p class="w-clock__date" id="date-masehi">{{ $now->isoFormat('dddd, D MMMM Y') }}</p>
                <p class="w-clock__hijri">
                    <i class="bi bi-moon-stars-fill" aria-hidden="true"></i>
                    <span><span id="date-hijri">&hellip;</span><small>Kalender Hijriah (perkiraan)</small></span>
                </p>
            </div>

            <div class="w-card w-prayer">
                <div class="w-prayer__head">
                    <p class="w-card__label"><i class="bi bi-calendar2-week-fill" aria-hidden="true"></i> Jadwal Shalat Hari Ini</p>
                    <span class="w-next" id="next-prayer" hidden>
                        <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                        <span>Menuju <span id="next-name"></span> &middot; <b id="next-countdown"></b></span>
                    </span>
                </div>
                <ul class="w-times">
                    @foreach ([
                        ['imsak', 'Imsak', 'bi-moon', true],
                        ['subuh', 'Subuh', 'bi-cloud-moon-fill', false],
                        ['terbit', 'Terbit', 'bi-sunrise-fill', true],
                        ['dzuhur', 'Dzuhur', 'bi-sun-fill', false],
                        ['ashar', 'Ashar', 'bi-cloud-sun-fill', false],
                        ['maghrib', 'Maghrib', 'bi-sunset-fill', false],
                        ['isya', 'Isya', 'bi-moon-stars-fill', false],
                    ] as [$key, $label, $icon, $minor])
                        <li class="w-time {{ $minor ? 'w-time--minor' : '' }}" data-prayer="{{ $key }}">
                            <i class="bi {{ $icon }}" aria-hidden="true"></i>
                            <span class="w-time__name">{{ $label }}</span>
                            <span class="w-time__at">--:--</span>
                        </li>
                    @endforeach
                </ul>
                <p class="w-note">
                    Wilayah {{ $loc['name'] }} &middot; Hisab kriteria Kemenag (Subuh {{ config('portal.prayer.fajr_angle') }}&deg;,
                    Isya {{ config('portal.prayer.isha_angle') }}&deg;, ihtiyat {{ config('portal.prayer.ihtiyat') }} menit).
                    Dapat berbeda 1&ndash;2 menit dari jadwal resmi.
                </p>
            </div>

            <div class="w-card w-qibla">
                <p class="w-card__label"><i class="bi bi-compass-fill" aria-hidden="true"></i> Arah Kiblat</p>
                <svg class="compass" viewBox="0 0 160 160" role="img"
                    aria-label="Kompas: kiblat {{ number_format($qibla, 1, ',', '.') }} derajat dari utara">
                    <circle cx="80" cy="80" r="74" fill="#f7f3ea" stroke="#24524f" stroke-width="2" />
                    <circle cx="80" cy="80" r="64" fill="none" stroke="#d8a93b" stroke-width="1" stroke-dasharray="2 4" />
                    @for ($deg = 0; $deg < 360; $deg += 30)
                        <line x1="80" y1="9" x2="80" y2="{{ $deg % 90 === 0 ? 19 : 15 }}" stroke="#24524f"
                            stroke-width="{{ $deg % 90 === 0 ? 2 : 1 }}" transform="rotate({{ $deg }} 80 80)" />
                    @endfor
                    <g font-family="Jost, sans-serif" font-weight="700" font-size="12" fill="#24524f" text-anchor="middle">
                        <text x="80" y="33" fill="#b23a3a">U</text>
                        <text x="131" y="84">T</text>
                        <text x="80" y="136">S</text>
                        <text x="29" y="84">B</text>
                    </g>
                    <g transform="rotate({{ round($qibla, 1) }} 80 80)">
                        <path d="M80 26 L86 80 L80 88 L74 80 Z" fill="#24524f" />
                        <path d="M80 88 L86 80 L80 120 L74 80 Z" fill="#c9c2b2" />
                        {{-- Ka'bah di ujung jarum --}}
                        <g transform="translate(80 18) rotate({{ -round($qibla, 1) }})">
                            <rect x="-8" y="-8" width="16" height="16" rx="1.5" fill="#1b1b1b" />
                            <rect x="-8" y="-4" width="16" height="3" fill="#d8a93b" />
                        </g>
                    </g>
                    <circle cx="80" cy="80" r="5" fill="#d8a93b" stroke="#fff" stroke-width="2" />
                </svg>
                <p class="w-qibla__deg">
                    {{ number_format($qibla, 1, ',', '.') }}&deg;
                    <small>dari Utara searah jarum jam</small>
                    <small>Arah {{ $qiblaDirection }} &middot; &plusmn; {{ number_format($kaabaDistance, 0, ',', '.') }} km ke Ka'bah</small>
                </p>
            </div>
        </section>

        {{-- ================= Layanan ================= --}}
        <section class="portal__panel" aria-labelledby="layanan-title">
            <h2 class="section-title" id="layanan-title"><svg aria-hidden="true"><use href="#i-star8" /></svg> Layanan &amp; Aplikasi</h2>

            @if ($portal_items->isEmpty())
                <p class="portal__empty">Belum ada layanan yang ditampilkan.</p>
            @else
                <ul class="portal__grid">
                    @foreach ($portal_items as $item)
                        <li>
                            @if (!$item->has_link)
                                <div class="portal-card portal-card--soon" style="--i: {{ $loop->index }}" aria-disabled="true">
                                    <span class="portal-card__icon"><img src="{{ $item->image_src }}" alt="" data-fallback></span>
                                    <span class="portal-card__title">{{ $item->title }}</span>
                                    <span class="portal-card__badge">Segera hadir</span>
                                </div>
                            @elseif ($item->is_modal)
                                <button type="button" class="portal-card" style="--i: {{ $loop->index }}"
                                    data-portal-modal data-url="{{ $item->destination_url }}" data-title="{{ $item->title }}">
                                    <i class="bi bi-window portal-card__hint" aria-hidden="true"></i>
                                    <span class="portal-card__icon"><img src="{{ $item->image_src }}" alt="" data-fallback></span>
                                    <span class="portal-card__title">{{ $item->title }}</span>
                                </button>
                            @else
                                <a class="portal-card" style="--i: {{ $loop->index }}" href="{{ $item->destination_url }}"
                                    target="_blank" rel="noopener">
                                    <i class="bi bi-box-arrow-up-right portal-card__hint" aria-hidden="true"></i>
                                    <span class="portal-card__icon"><img src="{{ $item->image_src }}" alt="" data-fallback></span>
                                    <span class="portal-card__title">{{ $item->title }}</span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <a class="org-link" href="{{ route('organization.index') }}">
            <span class="org-link__icon"><i class="bi bi-diagram-3-fill" aria-hidden="true"></i></span>
            <span class="org-link__text">
                <strong>Struktur Organisasi</strong>
                <span>Susunan unit kerja dan pegawai Kantor Kementerian Agama Kabupaten Pesisir Selatan</span>
            </span>
            <span class="org-link__cta">Lihat Bagan <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
        </a>
    </main>

    <footer class="site-footer">
        <div class="ornament" aria-hidden="true"><svg><use href="#i-star8" /></svg></div>
        <h5>SIMPUL SEHATI</h5>
        <p>Made with 💕 by ours Pranata Komputer</p>
    </footer>

    {{-- Pop-up untuk item portal yang diset "Buka dalam pop-up" --}}
    <div class="modal fade portal-modal" id="portal-modal" tabindex="-1" aria-labelledby="portal-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-6 fw-semibold" id="portal-modal-title"></h2>
                    <a href="#" id="portal-modal-external" class="btn btn-sm btn-light ms-auto me-2" target="_blank" rel="noopener">
                        Buka di tab baru <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    <button type="button" class="btn-close btn-close-white m-0" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <iframe id="portal-modal-frame" title="Konten layanan" src="about:blank"></iframe>
            </div>
        </div>
    </div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('arsha/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Ikon gagal dimuat (mis. URL eksternal mati) diganti ikon bawaan.
        document.querySelectorAll('img[data-fallback]').forEach(function (img) {
            function useDefault() {
                img.removeAttribute('data-fallback');
                img.src = @json(\App\Models\PortalItem::DEFAULT_IMAGE);
            }
            if (img.complete && img.naturalWidth === 0) {
                useDefault();
            } else {
                img.addEventListener('error', useDefault, { once: true });
            }
        });

        (function () {
            var modalEl = document.getElementById('portal-modal');
            var frame = document.getElementById('portal-modal-frame');
            var title = document.getElementById('portal-modal-title');
            var external = document.getElementById('portal-modal-external');
            var modal = new bootstrap.Modal(modalEl);

            document.querySelectorAll('[data-portal-modal]').forEach(function (button) {
                button.addEventListener('click', function () {
                    title.textContent = button.getAttribute('data-title');
                    external.href = button.getAttribute('data-url');
                    frame.src = button.getAttribute('data-url');
                    modal.show();
                });
            });

            // Hentikan halaman di dalam pop-up saat ditutup.
            modalEl.addEventListener('hidden.bs.modal', function () {
                frame.src = 'about:blank';
            });
        })();

        /*
         * Jam, tanggal Masehi & Hijriah, serta jadwal shalat.
         * Jadwal dihitung di browser (hisab) memakai algoritme posisi matahari
         * dengan kriteria Kemenag, jadi tidak bergantung pada layanan luar.
         */
        (function () {
            var CFG = @json($clientConfig);
            var TZ = CFG.location.timezone;
            var HIJRI_MONTHS = ['Muharram', 'Safar', 'Rabiul Awal', 'Rabiul Akhir', 'Jumadil Awal', 'Jumadil Akhir',
                'Rajab', "Sya'ban", 'Ramadhan', 'Syawal', "Dzulqa'dah", 'Dzulhijjah'];
            var PRAYER_LABELS = { subuh: 'Subuh', dzuhur: 'Dzuhur', ashar: 'Ashar', maghrib: 'Maghrib', isya: 'Isya' };

            var el = function (id) { return document.getElementById(id); };
            var pad = function (n) { return (n < 10 ? '0' : '') + n; };

            // Bagian tanggal/jam saat ini di zona waktu kantor (bukan zona waktu perangkat).
            var partsFmt = new Intl.DateTimeFormat('en-GB', {
                timeZone: TZ, year: 'numeric', month: 'numeric', day: 'numeric',
                hour: 'numeric', minute: 'numeric', second: 'numeric', hourCycle: 'h23'
            });
            function nowParts() {
                var out = {};
                partsFmt.formatToParts(new Date()).forEach(function (p) { out[p.type] = parseInt(p.value, 10); });
                return out;
            }

            var masehiFmt = new Intl.DateTimeFormat('id-ID', { timeZone: TZ, weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            var hijriFmt = null;
            try {
                hijriFmt = new Intl.DateTimeFormat('en-u-ca-islamic-umalqura-nu-latn', { timeZone: TZ, day: 'numeric', month: 'numeric', year: 'numeric' });
                if (hijriFmt.resolvedOptions().calendar.indexOf('islamic') !== 0) hijriFmt = null;
            } catch (e) { hijriFmt = null; }

            function hijriText(date) {
                if (!hijriFmt) return '-';
                var h = {};
                hijriFmt.formatToParts(date).forEach(function (p) { h[p.type] = p.value; });
                return parseInt(h.day, 10) + ' ' + HIJRI_MONTHS[parseInt(h.month, 10) - 1] + ' ' + parseInt(h.year, 10) + ' H';
            }

            // ---------- Hisab waktu shalat ----------
            var rad = function (d) { return d * Math.PI / 180; };
            var deg = function (r) { return r * 180 / Math.PI; };
            var fix = function (a, b) { a = a - b * Math.floor(a / b); return a < 0 ? a + b : a; };

            function julian(y, m, d) {
                if (m <= 2) { y -= 1; m += 12; }
                var A = Math.floor(y / 100), B = 2 - A + Math.floor(A / 4);
                return Math.floor(365.25 * (y + 4716)) + Math.floor(30.6001 * (m + 1)) + d + B - 1524.5;
            }

            function sun(jd) {
                var D = jd - 2451545.0;
                var g = fix(357.529 + 0.98560028 * D, 360);
                var q = fix(280.459 + 0.98564736 * D, 360);
                var L = fix(q + 1.915 * Math.sin(rad(g)) + 0.020 * Math.sin(rad(2 * g)), 360);
                var e = 23.439 - 0.00000036 * D;
                var RA = deg(Math.atan2(Math.cos(rad(e)) * Math.sin(rad(L)), Math.cos(rad(L)))) / 15;
                return { decl: deg(Math.asin(Math.sin(rad(e)) * Math.sin(rad(L)))), eqt: q / 15 - fix(RA, 24) };
            }

            function prayerTimes(y, m, d) {
                var lat = CFG.location.latitude, lng = CFG.location.longitude, tz = CFG.location.utc_offset;
                var jd = julian(y, m, d) - lng / (15 * 24);

                function noon(t) { return fix(12 - sun(jd + t).eqt, 24); }
                function angleTime(angle, t, before) {
                    var decl = sun(jd + t).decl;
                    var x = (-Math.sin(rad(angle)) - Math.sin(rad(decl)) * Math.sin(rad(lat))) /
                        (Math.cos(rad(decl)) * Math.cos(rad(lat)));
                    var h = deg(Math.acos(Math.max(-1, Math.min(1, x)))) / 15;
                    return noon(t) + (before ? -h : h);
                }
                function asr(t) {
                    var decl = sun(jd + t).decl;
                    var angle = -deg(Math.atan(1 / (1 + Math.tan(rad(Math.abs(lat - decl))))));
                    return angleTime(angle, t);
                }

                // Dua iterasi agar posisi matahari dihitung pada jam kejadian.
                var t = { fajr: 5, sunrise: 6, dhuhr: 12, asr: 13, sunset: 18, isha: 18 };
                for (var i = 0; i < 2; i++) {
                    var p = {};
                    Object.keys(t).forEach(function (k) { p[k] = t[k] / 24; });
                    t = {
                        fajr: angleTime(CFG.prayer.fajr_angle, p.fajr, true),
                        sunrise: angleTime(0.833, p.sunrise, true),
                        dhuhr: noon(p.dhuhr),
                        asr: asr(p.asr),
                        sunset: angleTime(0.833, p.sunset),
                        isha: angleTime(CFG.prayer.isha_angle, p.isha)
                    };
                }

                var shift = tz - lng / 15;
                var ih = CFG.prayer.ihtiyat / 60;
                var minutes = function (h) { return Math.ceil((h + shift) * 60 - 1e-6); };
                var res = {
                    subuh: minutes(t.fajr + ih),
                    terbit: Math.floor((t.sunrise + shift - ih) * 60),
                    dzuhur: minutes(t.dhuhr + ih),
                    ashar: minutes(t.asr + ih),
                    maghrib: minutes(t.sunset + ih),
                    isya: minutes(t.isha + ih)
                };
                res.imsak = res.subuh - CFG.prayer.imsak_minutes;
                return res; // menit sejak tengah malam
            }

            var hm = function (min) { return pad(Math.floor(min / 60) % 24) + ':' + pad(min % 60); };

            var dayKey = null, today = null, tomorrow = null;
            var cells = {};
            document.querySelectorAll('[data-prayer]').forEach(function (li) { cells[li.getAttribute('data-prayer')] = li; });

            function renderDay(p) {
                dayKey = p.year + '-' + p.month + '-' + p.day;
                today = prayerTimes(p.year, p.month, p.day);
                var next = new Date(Date.UTC(p.year, p.month - 1, p.day + 1));
                tomorrow = prayerTimes(next.getUTCFullYear(), next.getUTCMonth() + 1, next.getUTCDate());
                Object.keys(cells).forEach(function (k) {
                    cells[k].querySelector('.w-time__at').textContent = hm(today[k]);
                });
                el('date-masehi').textContent = masehiFmt.format(new Date());
                el('date-hijri').textContent = hijriText(new Date());
            }

            function tick() {
                var p = nowParts();
                if (dayKey !== p.year + '-' + p.month + '-' + p.day) renderDay(p);

                el('clock-hm').textContent = pad(p.hour) + ':' + pad(p.minute);
                el('clock-s').textContent = ':' + pad(p.second);

                // Shalat berikutnya + hitung mundur
                var nowSec = p.hour * 3600 + p.minute * 60 + p.second;
                var nextKey = null, nextSec = null;
                ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'].some(function (k) {
                    if (today[k] * 60 > nowSec) { nextKey = k; nextSec = today[k] * 60; return true; }
                    return false;
                });
                if (!nextKey) { nextKey = 'subuh'; nextSec = 86400 + tomorrow.subuh * 60; }

                var left = nextSec - nowSec;
                el('next-name').textContent = PRAYER_LABELS[nextKey];
                el('next-countdown').textContent = pad(Math.floor(left / 3600)) + ':' + pad(Math.floor(left % 3600 / 60)) + ':' + pad(left % 60);
                el('next-prayer').hidden = false;

                Object.keys(cells).forEach(function (k) {
                    cells[k].classList.toggle('is-next', k === nextKey && nextSec < 86400);
                });
            }

            tick();
            setInterval(tick, 1000);
        })();
    </script>

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/6147f11cd326717cb6824d14/1fg0gbm69';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <!--End of Tawk.to Script-->

</body>

</html>
