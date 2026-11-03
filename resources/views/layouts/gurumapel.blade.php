<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Portal Guru Mapel SMANDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 260px; height: 100vh; background-color: #1b3167; position: fixed; top: 0; left: 0; color: white; display: flex; flex-direction: column; justify-content: space-between; padding: 20px; }
        .sidebar .brand { font-weight: bold; font-size: 1.1rem; letter-spacing: 0.5px; margin-bottom: 30px; }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.8); padding: 10px 15px; border-radius: 8px; margin-bottom: 5px; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 10px;}
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255, 255, 255, 0.15); color: white; }
        .main-content { margin-left: 260px; padding: 30px; }
        .card-stat { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

    <!-- Sidebar Guru Mapel -->
    <div class="sidebar">
        <div>
            <div class="brand">
                <i class="fa-solid fa-graduation-cap"></i> SMANDA PRESENSI<br>
                <small class="text-white-50" style="font-size: 11px;">PORTAL GURU MAPEL</small>
            </div>
            <div class="nav flex-column">
                <a href="{{ route('gurumapel.dashboard') }}" class="nav-link {{ request()->routeIs('gurumapel.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> Dashboard</a>
                <a href="{{ route('gurumapel.kbm') }}" class="nav-link {{ request()->routeIs('gurumapel.kbm*') ? 'active' : '' }}"><i class="fa-solid fa-chalkboard-user"></i> Verifikasi Fisik KBM</a>
            </div>
        </div>

        <div>
            <div class="p-2 bg-dark bg-opacity-25 rounded-3 mb-3 small">
                <strong>{{ Auth::user()->name }}</strong><br>
                <span class="text-white-50" style="font-size: 11px;">GURU MATA PELAJARAN</span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger w-100 btn-sm py-2 rounded-3 text-white fw-semibold" style="background-color: #ff4d4d; border:none;">Logout Akun</button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>