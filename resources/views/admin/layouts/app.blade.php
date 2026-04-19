<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Booking Lapangan</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-light: #3b82f6;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
            --light-bg: #f8fafc;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --sidebar-width: 280px;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--light-bg) 0%, #f1f5f9 100%);
            overflow-x: hidden;
        }
        
        /* Parallax Background */
        .main-content::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 120%;
            height: 120%;
            background: radial-gradient(circle at 20% 80%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
            z-index: -1;
            animation: parallaxFloat 20s ease-in-out infinite;
        }
        
        @keyframes parallaxFloat {
            0%, 100% { transform: translateX(-10px) translateY(-10px) rotate(0deg); }
            50% { transform: translateX(10px) translateY(10px) rotate(1deg); }
        }
        
        /* Card Animations */
        .card {
            border: 1px solid var(--gray-200);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.75rem;
            background: var(--white);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: slideInUp 0.6s ease-out;
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Staggered Animation */
        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }
        
        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            min-height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--gray-200);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .sidebar .nav-link {
            color: var(--gray-700);
            padding: 0.75rem 1rem;
            margin: 0.125rem 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.1), transparent);
            transition: left 0.5s;
        }
        
        .sidebar .nav-link:hover::before {
            left: 100%;
        }
        
        .sidebar .nav-link:hover {
            color: var(--primary-color);
            background-color: var(--gray-50);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            color: var(--primary-color);
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            transform: translateX(5px);
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            background-color: var(--light-bg);
            min-height: 100vh;
            padding: 20px;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--gray-800) !important;
            font-size: 1.25rem;
        }
        
        .card-header {
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            font-weight: 600;
            color: var(--gray-800);
        }
        
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }
        
        .table {
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            background: var(--white);
        }
        
        .table th {
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            border-color: var(--gray-200);
            font-weight: 600;
            color: var(--gray-700);
            padding: 1rem 0.75rem;
            position: relative;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .table th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--info-color));
        }
        
        .table tbody tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 1px solid var(--gray-100);
        }
        
        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%);
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .table tbody tr:nth-child(even) {
            background-color: rgba(248, 250, 252, 0.5);
        }
        
        .table tbody tr:nth-child(even):hover {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(16, 185, 129, 0.08) 100%);
        }
        
        .table td {
            padding: 0.875rem 0.75rem;
            vertical-align: middle;
            border-color: var(--gray-100);
        }
        
        .form-control, .form-select {
            border: 1px solid var(--gray-300);
            border-radius: 0.5rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }
        
        .alert {
            border-radius: 0.75rem;
            border: none;
            animation: slideInDown 0.5s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .bg-success {
            background-color: var(--success-color) !important;
        }
        
        .bg-danger {
            background-color: var(--danger-color) !important;
        }
        
        .bg-info {
            background-color: var(--info-color) !important;
        }
        
        .bg-warning {
            background-color: var(--warning-color) !important;
        }
        
        /* Badge Animations */
        .badge {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .badge:hover {
            transform: scale(1.1);
        }
        
        .badge:hover::before {
            left: 100%;
        }
        
        /* Status Badge Colors */
        .badge.bg-success {
            background: linear-gradient(135deg, var(--success-color), #059669) !important;
        }
        
        .badge.bg-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626) !important;
        }
        
        .badge.bg-warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706) !important;
        }
        
        .badge.bg-info {
            background: linear-gradient(135deg, var(--info-color), #0891b2) !important;
        }
        
        .badge.bg-primary {
            background: linear-gradient(135deg, var(--primary-color), #1d4ed8) !important;
        }
        
        .badge.bg-secondary {
            background: linear-gradient(135deg, var(--secondary-color), #475569) !important;
        }
        
        .live-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                height: 100vh;
                z-index: 1050;
                transition: left 0.3s ease;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            .main-content {
                margin-left: 0 !important;
                width: 100%;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="p-4">
            <h4 class="text-black mb-4">
                <i class="bi bi-calendar-check"></i>
                Booking Lapangan
            </h4>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" 
                   href="{{ route('admin.bookings.index') }}">
                    <i class="bi bi-calendar-event me-2"></i>
                    Booking
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.live-booking.*') ? 'active' : '' }}" 
                   href="{{ route('admin.live-booking.index') }}">
                    <i class="bi bi-broadcast me-2"></i>
                    Live Booking
                    <span class="live-indicator ms-2"></span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.member-schedules.*') ? 'active' : '' }}" 
                   href="{{ route('admin.member-schedules.index') }}">
                    <i class="bi bi-calendar-week me-2"></i>
                    Jadwal Member
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}" 
                   href="{{ route('admin.transactions.index') }}">
                    <i class="bi bi-receipt me-2"></i>
                    Pembukuan
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.stock-transfers.*') ? 'active' : '' }}" 
                   href="{{ route('admin.stock-transfers.index') }}">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Stok Gudang/Display
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.pos.*') ? 'active' : '' }}" 
                   href="{{ route('admin.pos.index') }}">
                    <i class="bi bi-cart me-2"></i>
                    POS
                </a>
            </li>
            
            @canany(['view reports'])
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                   href="{{ route('admin.reports.index') }}">
                    <i class="bi bi-graph-up me-2"></i>
                    Laporan
                </a>
            </li>
            @endcanany
            
            @canany(['view branches', 'view fields', 'view users'])
            <li class="nav-item mt-3">
                <h6 class="text-white-50 px-3 mb-2">MANAJEMEN</h6>
            </li>
            
            @can('view branches')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" 
                   href="{{ route('admin.products.index') }}">
                    <i class="bi bi-box-seam me-2"></i>
                    Produk
                </a>
            </li>
            @endcan
            
            @can('view branches')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.branches.*') ? 'active' : '' }}" 
                   href="{{ route('admin.branches.index') }}">
                    <i class="bi bi-building me-2"></i>
                    Cabang
                </a>
            </li>
            @endcan
            
            @can('view fields')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.fields.*') ? 'active' : '' }}" 
                   href="{{ route('admin.fields.index') }}">
                    <i class="bi bi-grid me-2"></i>
                    Lapangan
                </a>
            </li>
            @endcan
            
            @can('view users')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                   href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people me-2"></i>
                    Pengguna
                </a>
            </li>
            @endcan
            

            
            @can('view branches')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.pricing-rules.*') ? 'active' : '' }}" 
                   href="{{ route('admin.pricing-rules.index') }}">
                    <i class="bi bi-currency-dollar me-2"></i>
                    Aturan Harga
                </a>
            </li>
            @endcan
            @endcanany
        </ul>
        
            <ul class="nav flex-column">
                @can('view branches')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                       href="{{ route('admin.settings.index') }}">
                        <i class="bi bi-gear me-2"></i>
                        Pengaturan
                    </a>
                </li>
                @endcan
            </ul>
            
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-2"></i>
                    {{ auth()->user()->name }}
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none" type="button" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="ms-auto d-flex align-items-center">
                    <!-- Timezone Dropdown -->
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-clock me-1"></i>
                            {{ \App\Models\Setting::get('app_timezone', 'Asia/Jakarta') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Pilih Zona Waktu</h6></li>
                            <li><a class="dropdown-item" href="#" onclick="changeTimezone('Asia/Jakarta')">WIB (Jakarta)</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeTimezone('Asia/Makassar')">WITA (Makassar)</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeTimezone('Asia/Jayapura')">WIT (Jayapura)</a></li>
                        </ul>
                    </div>
                    
                    <span class="text-muted">
                        <i class="bi bi-calendar me-1"></i>
                        {{ now()->format('d F Y') }}
                        <i class="bi bi-clock ms-2 me-1"></i>
                        {{ now()->format('H:i:s') }}
                    </span>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
        
        // Auto hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Change timezone function
        function changeTimezone(timezone) {
            fetch('/admin/settings', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ app_timezone: timezone })
            })
            .then(response => response.json())
            .then(data => {
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
    
    @stack('scripts')
</body>
</html>