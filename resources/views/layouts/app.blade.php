<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        /* Reset body margin dan padding */
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fc;
            overflow-x: hidden;
        }
        
        /* Main wrapper untuk seluruh layout */
        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar yang menyatu dengan navbar */
        .sidebar {
            width: 280px;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            left: 0;
            top: 0; /* Mulai dari atas */
            height: 100vh; /* Penuh tinggi viewport */
            z-index: 1030; /* Sama dengan navbar */
            transition: all 0.3s ease;
            /* Hilangkan scrollbar dan nonaktifkan scroll */
            overflow: hidden; /* Nonaktifkan scroll sama sekali */
        }
        
        /* Header Sidebar yang menjadi bagian navbar */
        .sidebar-header {
            height: 70px;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h6 {
            font-weight: 700;
            margin: 0;
            font-size: 1.1rem;
        }
        
        /* Konten sidebar */
        .sidebar-content {
            padding: 25px 20px;
            height: calc(100vh - 70px); /* Tinggi penuh dikurangi header */
            overflow-y: auto; /* Hanya konten yang bisa scroll */
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        .sidebar-content::-webkit-scrollbar {
            display: none;
        }
        
        /* Navbar utama */
        .navbar-fixed {
            position: fixed;
            top: 0;
            left: 280px; /* Mulai setelah sidebar */
            right: 0;
            height: 70px;
            z-index: 1030;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background: #667EEA !important;
            border: none;
            transition: all 0.3s ease;
        }
        
        /* Konten utama */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
            margin-top: 70px; /* Beri margin top untuk navbar */
            min-height: calc(100vh - 70px);
            transition: all 0.3s ease;
        }
        
        /* Styling untuk tombol quick actions */
        .quick-action-btn {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 12px;
            border: none;
            border-radius: 12px;
            text-align: left;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .quick-action-btn i {
            font-size: 1.3rem;
            margin-right: 15px;
            width: 25px;
            text-align: center;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            color: white;
            text-decoration: none;
        }
        
        /* Warna tombol */
        .btn-surat-masuk { 
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); 
        }
        .btn-surat-keluar { 
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); 
        }
        
        /* Navigation menu */
        .nav-section {
            margin-bottom: 30px;
        }
        
        .nav-section-title {
            color: #6e707e;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 15px;
            padding-left: 8px;
            letter-spacing: 0.5px;
        }
        
        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            color: #5a5c69;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            margin-bottom: 6px;
            font-weight: 600;
            border: 1px solid transparent;
        }
        
        .nav-link-custom i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .nav-link-custom:hover {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
            color: #4e73df;
            transform: translateX(5px);
            border-color: #e3e6f0;
        }
        
        .nav-link-custom.active {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
        }
        
        /* Warna teks navbar */
        .navbar-light .navbar-brand,
        .navbar-light .navbar-nav .nav-link {
            color: white !important;
            font-weight: 600;
        }
        
        .navbar-light .navbar-toggler {
            border-color: rgba(255,255,255,0.3);
        }
        
        .navbar-light .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        /* Toggle button untuk sidebar di mobile */
        .sidebar-toggle-btn {
            display: none !important;
        }
        
        /* Dropdown menu styling - POSITION FIXED */
        .navbar-light .navbar-nav .dropdown-menu {
            background: white;
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0.5rem;
            margin-top: 10px;
            position: fixed !important;
            top: 60px !important;
            right: 20px !important;
            left: auto !important;
            transform: none !important;
        }
        
        .navbar-light .navbar-nav .dropdown-item {
            color: #5a5c69;
            padding: 0.75rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .navbar-light .navbar-nav .dropdown-item:hover {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
        }
        
        /* User dropdown khusus */
        .user-dropdown {
            position: relative;
        }
        
        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 25px;
            background: rgba(255,255,255,0.15);
            transition: all 0.3s ease;
            border: none;
            outline: none;
        }
        
        .user-dropdown .dropdown-toggle:hover,
        .user-dropdown .dropdown-toggle:focus {
            background: rgba(255,255,255,0.25);
            color: white !important;
        }
        
        .user-dropdown .dropdown-toggle::after {
            margin-left: 5px;
        }
        
        /* Hilangkan efek show dari Bootstrap yang menggeser elemen */
        .user-dropdown .dropdown-toggle.show {
            background: rgba(255,255,255,0.25) !important;
            color: white !important;
        }
        
        /* Untuk mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                z-index: 1040;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .navbar-fixed {
                left: 0;
                right: 0;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
                margin-top: 70px;
            }
            
            /* Tampilkan toggle button untuk sidebar di mobile */
            .sidebar-toggle-btn {
                display: block !important;
                background: none;
                border: none;
                font-size: 1.3rem;
                color: white;
                padding: 8px 12px;
                margin-right: 10px;
            }
            
            /* Saat sidebar aktif di mobile, geser konten */
            .sidebar.active ~ .main-content {
                margin-left: 280px;
            }
            
            .sidebar.active ~ .navbar-fixed {
                left: 280px;
            }
            
            /* Navbar collapse - selalu tampilkan di mobile */
            .navbar-collapse {
                display: flex !important;
                flex-basis: auto;
            }
            
            /* User dropdown di mobile - hanya tampilkan icon */
            .user-dropdown .dropdown-toggle span {
                display: none;
            }
            
            .user-dropdown .dropdown-toggle {
                padding: 8px 12px;
                background: rgba(255,255,255,0.2);
            }
            
            .user-dropdown .dropdown-toggle i {
                margin-right: 0;
                font-size: 1.4rem;
            }
            
            /* Hilangkan navbar toggler default */
            .navbar-toggler {
                display: none !important;
            }
            
            /* Adjust dropdown position untuk mobile */
            .navbar-light .navbar-nav .dropdown-menu {
                top: 65px !important;
                right: 15px !important;
                min-width: 160px;
            }
        }

        /* Desktop styles */
        @media (min-width: 769px) {
            .navbar-collapse {
                display: flex !important;
                flex-basis: auto;
            }
            
            /* Dropdown position untuk desktop */
            .navbar-light .navbar-nav .dropdown-menu {
                top: 65px !important;
                right: 25px !important;
            }
        }

        /* Smooth scroll behavior */
        html {
            scroll-padding-top: 70px;
        }
        
        /* Logo/brand styling */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        
        /* Container adjustment */
        .navbar > .container {
            padding-left: 25px;
            padding-right: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        /* Navbar content styling */
        .navbar-content {
            display: flex;
            align-items: center;
            width: 100%;
            justify-content: space-between;
        }
        
        .navbar-left {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        
        .navbar-right {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        
        /* Ensure dropdown works properly */
        .dropdown-menu.show {
            display: block;
        }
        
        /* Prevent layout shift when dropdown opens */
        .user-dropdown .dropdown-toggle {
            position: relative;
            z-index: 1031;
        }
    </style>
</head>
<body>
    <div id="app">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Header Sidebar -->
            <div class="sidebar-header">
                <h6><i class="fas fa-archive me-2"></i>Arsip Surat</h6>
            </div>
            
            <!-- Konten Sidebar -->
            <div class="sidebar-content">
                <!-- Navigation Menu -->
                <div class="nav-section">
                    <div class="nav-section-title">Menu Navigasi</div>
                    <a href="{{ url('/') }}" class="nav-link-custom {{ request()->is('/') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Beranda
                    </a>
                    <a href="{{ route('surat-masuk.index') }}" class="nav-link-custom {{ request()->is('surat-masuk*') ? 'active' : '' }}">
                        <i class="fas fa-envelope"></i>
                        Surat Masuk
                    </a>
                    <a href="{{ route('surat-keluar.index') }}" class="nav-link-custom {{ request()->is('surat-keluar*') ? 'active' : '' }}">
                        <i class="fas fa-paper-plane"></i>
                        Surat Keluar
                    </a>
                    <a href="{{ route('laporan.index') }}" class="nav-link-custom {{ request()->is('laporan*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        Laporan
                    </a>
                </div>

                <!-- Quick Actions - Hanya untuk Admin/Staff -->
                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
                <div class="nav-section">
                    <div class="nav-section-title">Aksi Cepat</div>
                    
                    <a href="{{ route('surat-masuk.create') }}" class="quick-action-btn btn-surat-masuk">
                        <i class="fas fa-plus-circle"></i>
                        <div>
                            <div>Tambah Surat Masuk</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('surat-keluar.create') }}" class="quick-action-btn btn-surat-keluar">
                        <i class="fas fa-plus-circle"></i>
                        <div>
                            <div>Tambah Surat Keluar</div>
                        </div>
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm fixed-top navbar-fixed">
            <div class="container">
                <div class="navbar-content">
                    <div class="navbar-left">
                        <button class="sidebar-toggle-btn" type="button" onclick="toggleSidebar()">
                            <i class="fas fa-bars"></i>
                        </button>
                        
                        <a class="navbar-brand" href="{{ url('/') }}">
                            <i class="fas fa-home me-2"></i>Beranda
                        </a>
                    </div>

                    <div class="navbar-right">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                            @endif

                            @if (Route::has('register'))
                                <a class="nav-link" href="{{ route('register') }}">Daftar</a>
                            @endif
                        @else
                            <div class="nav-item dropdown user-dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle me-1"></i>
                                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <!-- Tambahkan link profil di sini -->
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="fas fa-user me-2"></i>Profil Saya
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Keluar
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <!-- Konten Utama -->
        <div class="main-content" id="mainContent">
            <main class="py-2">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Toggle sidebar untuk mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Optional: Tambahkan efek shadow saat scroll
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar-fixed');
            
            window.addEventListener('scroll', function() {
                if (window.scrollY > 10) {
                    navbar.style.boxShadow = '0 2px 15px rgba(0,0,0,0.2)';
                } else {
                    navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                }
            });

            // Close sidebar ketika klik di luar pada mobile
            document.addEventListener('click', function(event) {
                const sidebar = document.getElementById('sidebar');
                const sidebarToggleBtn = document.querySelector('.sidebar-toggle-btn');
                
                if (window.innerWidth <= 768 && 
                    sidebar.classList.contains('active') &&
                    !sidebar.contains(event.target) &&
                    !sidebarToggleBtn.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            });

            // Prevent layout shift when dropdown is clicked
            document.addEventListener('show.bs.dropdown', function(e) {
                e.target.classList.add('show');
            });
            
            document.addEventListener('hide.bs.dropdown', function(e) {
                e.target.classList.remove('show');
            });
        });
    </script>
    
    @stack('scripts')

    <!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>