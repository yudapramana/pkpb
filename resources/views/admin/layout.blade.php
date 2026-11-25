<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') - Struktur Organisasi</title>

    <link href="{{ asset('arsha/assets/img/favicon.png') }}" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('arsha/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('arsha/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <style>
        :root {
            --admin-teal: #24524f;
        }

        body {
            font-family: "Poppins", sans-serif;
            font-size: 14px;
            background: #f4f5f4;
        }

        .admin-nav {
            background: var(--admin-teal);
        }

        .admin-nav .nav-link {
            color: rgba(255, 255, 255, .75);
        }

        .admin-nav .nav-link:hover,
        .admin-nav .nav-link.active {
            color: #fff;
        }

        .btn-teal {
            --bs-btn-color: #fff;
            --bs-btn-bg: var(--admin-teal);
            --bs-btn-border-color: var(--admin-teal);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #1b3f3d;
            --bs-btn-hover-border-color: #1b3f3d;
            --bs-btn-active-bg: #1b3f3d;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            flex: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #dfe7e6;
            color: var(--admin-teal);
            font-weight: 600;
            font-size: .85rem;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .page-header {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 1.4rem;
            font-weight: 600;
            margin: 0;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark admin-nav">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-diagram-3"></i> Admin Struktur Organisasi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav"
                aria-controls="adminNav" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}"
                            href="{{ route('admin.employees.index') }}">Pegawai</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.work-units.*') ? 'active' : '' }}"
                            href="{{ route('admin.work-units.index') }}">Unit Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('organization.index') }}" target="_blank" rel="noopener">
                            Lihat Halaman <i class="bi bi-box-arrow-up-right small"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('organization.simple') }}" target="_blank" rel="noopener">
                            Versi Word <i class="bi bi-box-arrow-up-right small"></i>
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="{{ asset('arsha/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Konfirmasi untuk form yang punya atribut data-confirm (mis. tombol hapus).
        document.addEventListener('submit', function (event) {
            var message = event.target.getAttribute('data-confirm');
            if (message && !window.confirm(message)) {
                event.preventDefault();
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
