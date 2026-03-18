<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Booking Lapangan</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        
        .login-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        
        .login-right {
            padding: 60px 40px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .demo-accounts {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .demo-account {
            background: white;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .demo-account:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .login-left {
                padding: 40px 20px;
            }
            
            .login-right {
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="login-card">
                <div class="row g-0">
                    <!-- Left Side - Branding -->
                    <div class="col-lg-6">
                        <div class="login-left">
                            <div>
                                <i class="bi bi-calendar-check" style="font-size: 4rem; margin-bottom: 20px;"></i>
                                <h2 class="fw-bold mb-3">Booking Lapangan</h2>
                                <p class="mb-4 opacity-75">Sistem manajemen booking lapangan yang modern dan efisien</p>
                                
                                <div class="row text-center">
                                    <div class="col-4">
                                        <i class="bi bi-lightning-charge fs-1 mb-2"></i>
                                        <small class="d-block">Realtime</small>
                                    </div>
                                    <div class="col-4">
                                        <i class="bi bi-shield-check fs-1 mb-2"></i>
                                        <small class="d-block">Aman</small>
                                    </div>
                                    <div class="col-4">
                                        <i class="bi bi-graph-up fs-1 mb-2"></i>
                                        <small class="d-block">Laporan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side - Login Form -->
                    <div class="col-lg-6">
                        <div class="login-right">
                            <div class="text-center mb-4">
                                <h3 class="fw-bold text-dark">Selamat Datang</h3>
                                <p class="text-muted">Silakan login untuk melanjutkan</p>
                            </div>
                            
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="bi bi-envelope text-muted"></i>
                                        </span>
                                        <input id="email" type="email" 
                                               class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                               name="email" value="{{ old('email') }}" 
                                               placeholder="Masukkan email Anda"
                                               required autocomplete="email" autofocus>
                                    </div>
                                    @error('email')
                                        <div class="text-danger mt-1">
                                            <small>{{ $message }}</small>
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="bi bi-lock text-muted"></i>
                                        </span>
                                        <input id="password" type="password" 
                                               class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                               name="password" 
                                               placeholder="Masukkan password Anda"
                                               required autocomplete="current-password">
                                    </div>
                                    @error('password')
                                        <div class="text-danger mt-1">
                                            <small>{{ $message }}</small>
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                                               {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Ingat saya
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Masuk
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Demo Accounts -->
                            <div class="demo-accounts">
                                <h6 class="fw-semibold mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Akun Demo
                                </h6>
                                
                                <div class="demo-account" onclick="fillLogin('owner@booking.com', 'password')">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">Owner</div>
                                            <small class="text-muted">owner@booking.com</small>
                                        </div>
                                        <span class="badge bg-primary">Full Access</span>
                                    </div>
                                </div>
                                
                                <div class="demo-account" onclick="fillLogin('admin1@booking.com', 'password')">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">Admin Cabang Jakarta</div>
                                            <small class="text-muted">admin1@booking.com</small>
                                        </div>
                                        <span class="badge bg-success">Admin</span>
                                    </div>
                                </div>
                                
                                <div class="demo-account" onclick="fillLogin('staff1@booking.com', 'password')">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">Staff Cabang Jakarta</div>
                                            <small class="text-muted">staff1@booking.com</small>
                                        </div>
                                        <span class="badge bg-info">Staff</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>