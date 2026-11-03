<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SMANDA Presensi</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        .btn-custom {
            background-color: #1b3167;
            color: white;
            font-weight: 600;
        }
        .btn-custom:hover {
            background-color: #13234d;
            color: white;
        }
    </style>
</head>
<body>

    <div class="login-card text-center">
        <!-- Logo Sekolah -->
        <div class="mb-3">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#1b3167" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div>
        <h4 class="fw-bold text-dark mb-1">SMANDA PRESENSI</h4>
        <p class="text-muted small mb-4">Silakan login ke portal Anda</p>

        @if($errors->any())
            <div class="alert alert-danger py-2 small" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST" class="text-start">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Alamat Email</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda..." value="{{ old('email') }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold text-muted">Kata Sandi</label>
                <input type="password" name="password" class="form-control" placeholder="*************" required>
            </div>
            <button type="submit" class="btn btn-custom w-100 py-2 rounded-3">Masuk ke Sistem</button>
        </form>

        <div class="mt-4 text-muted" style="font-size: 12px;">
            &copy; 2026 SMAN 2 Cilacap. All rights reserved.
        </div>
    </div>

</body>
</html>