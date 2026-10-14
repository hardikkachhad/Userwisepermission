<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Laravel Role & Permission System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
        }
        .demo-pill {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .demo-pill:hover {
            transform: scale(1.02);
            filter: brightness(0.95);
        }
    </style>
</head>
<body>
    <div class="login-card p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="d-inline-block p-3 rounded-circle bg-primary bg-opacity-10 text-primary mb-2">
                <i class="bi bi-shield-check fs-1"></i>
            </div>
            <h3 class="fw-bold text-dark">Welcome Back</h3>
            <p class="text-muted small">Custom Role & Permission RBAC Login</p>
        </div>

        @if(session('info'))
            <div class="alert alert-info py-2 small">{{ session('info') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', 'admin@example.com') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" value="password123" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small text-muted" for="remember">Remember me</label>
                </div>
                <a href="{{ route('register') }}" class="small text-decoration-none text-primary">New user? Register</a>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </button>
        </form>

        <!-- Quick Demo Credentials Selector -->
        <div class="mt-4 pt-3 border-top">
            <p class="text-muted text-center small fw-bold mb-2">⚡ QUICK DEMO CREDENTIALS (CLICK TO FILL):</p>
            <div class="d-grid gap-2">
                <div class="demo-pill p-2 border rounded bg-light d-flex justify-content-between align-items-center" onclick="fillLogin('admin@example.com', 'password123')">
                    <div>
                        <span class="badge bg-danger">Admin</span>
                        <span class="small fw-semibold ms-1">admin@example.com</span>
                    </div>
                    <span class="text-muted small"><i class="bi bi-arrow-return-left"></i> Fill</span>
                </div>

                <div class="demo-pill p-2 border rounded bg-light d-flex justify-content-between align-items-center" onclick="fillLogin('user@example.com', 'password123')">
                    <div>
                        <span class="badge bg-primary">User</span>
                        <span class="small fw-semibold ms-1">user@example.com</span>
                    </div>
                    <span class="text-muted small"><i class="bi bi-arrow-return-left"></i> Fill</span>
                </div>

                <div class="demo-pill p-2 border rounded bg-light d-flex justify-content-between align-items-center" onclick="fillLogin('manager@example.com', 'password123')">
                    <div>
                        <span class="badge bg-success">User + Direct Perms</span>
                        <span class="small fw-semibold ms-1">manager@example.com</span>
                    </div>
                    <span class="text-muted small"><i class="bi bi-arrow-return-left"></i> Fill</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
