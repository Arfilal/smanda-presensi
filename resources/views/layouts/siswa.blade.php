<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SMANDA Presensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        /* Wrapper utama tanpa bayangan */
        .app-wrapper {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            background-color: #ffffff;
            min-height: 100vh;
            overflow: hidden;
        }
        /* Navbar Atas */
        .app-navbar {
            background-color: #1b3167;
            color: white;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        /* Sidebar Drawer tanpa bayangan timbul di kiri */
        .app-sidebar {
            position: absolute;
            top: 0;
            left: -50%;
            width: 50%;
            height: 100%;
            background-color: #1b3167;
            color: white;
            z-index: 2000;
            transition: left 0.3s ease-in-out;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .app-sidebar.show { left: 0; }
        .sidebar-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.3);
            z-index: 1500;
            display: none;
        }
        .sidebar-overlay.show { display: block; }
        .app-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
            font-size: 13px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .app-sidebar .nav-link:hover, .app-sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
        }
        /* Card Styling Flat */
        .card-custom {
            border: none;
            border-radius: 12px;
            background: #ffffff;
            margin-bottom: 15px;
            border: 1px solid #eaeaea;
        }
    </style>
</head>
<body>

    <div class="app-wrapper">
        
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar Drawer -->
        <div class="app-sidebar" id="appSidebar">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold fs-6">MENU SISWA</span>
                    <button class="btn btn-sm text-white p-0 border-0 bg-transparent" onclick="toggleSidebar()"><i class="fa-solid fa-xmark fs-5"></i></button>
                </div>
                <div class="nav flex-column">
                    <a href="{{ route('siswa.dashboard') }}" class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> Dashboard</a>
                    <a href="{{ route('siswa.presensi') }}" class="nav-link {{ request()->routeIs('siswa.presensi*') ? 'active' : '' }}"><i class="fa-solid fa-qrcode"></i> Presensi GPS</a>
                    <a href="{{ route('siswa.izin') }}" class="nav-link {{ request()->routeIs('siswa.izin*') ? 'active' : '' }}"><i class="fa-solid fa-file-pen"></i> Izin / Sakit</a>
                    <a href="{{ route('siswa.riwayat') }}" class="nav-link {{ request()->routeIs('siswa.riwayat*') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Absen</a>
                    <a href="{{ route('siswa.profile') }}" class="nav-link {{ request()->routeIs('siswa.profile*') ? 'active' : '' }}"><i class="fa-solid fa-user"></i> Profil Akun</a>
                </div>
            </div>
            <div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm w-100 fw-semibold rounded-pill py-2" style="background-color: #ff4d4d; border:none; font-size: 12px;">Logout Akun</button>
                </form>
            </div>
        </div>

        <!-- Navbar Atas -->
        <div class="app-navbar">
            <button class="btn btn-sm text-white fs-5 p-0 border-0 bg-transparent" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <span class="fw-bold text-uppercase" style="font-size: 13px; letter-spacing: 0.5px;">Portal Siswa - SMAN 2 Cilacap</span>
            <div style="width: 24px;"></div>
        </div>

        <!-- Main Content Container -->
        <div class="p-3 bg-light min-vh-100">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show small py-2" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show small py-2" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>

    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('appSidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>