<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Credai Tree Plantation') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body, html {
            font-family: 'Inter', sans-serif;
            height: 100%;
            overflow-x: hidden;
        }
        
        .main-container {
            background: linear-gradient(135deg, #a8d5ba 0%, #7fb093 50%, #6b9f7d 100%);
            min-height: 100vh;
            display: flex;
            padding: 20px;
            gap: 20px;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px) saturate(150%);
            -webkit-backdrop-filter: blur(20px) saturate(150%);
            border-radius: 20px;
            padding: 30px 20px;
            box-shadow: 
                0 8px 32px rgba(31, 38, 135, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.18);
            height: fit-content;
            position: sticky;
            top: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
        }
        
        .sidebar-logo img {
            height: 60px;
            width: auto;
        }
        
        .sidebar-close-btn {
            display: none;
            background: transparent;
            border: none;
            color: #6b7280;
            font-size: 20px;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .sidebar-close-btn:hover {
            background: rgba(0, 0, 0, 0.05);
            color: #065f46;
        }
        
        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            margin-bottom: 8px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            text-decoration: none;
            color: #6b7280;
            font-weight: 500;
            font-size: 15px;
            border-radius: 12px;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .nav-link:hover, .nav-link.active {
            background: #065f46;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(6, 95, 70, 0.3);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }
        
        .nav-divider {
            height: 1px;
            background: rgba(0, 0, 0, 0.1);
            margin: 30px 0;
        }
        
        .user-section {
            margin-top: auto;
            padding-top: 30px;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: calc(100vw - 320px);
        }
        
        .top-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #065f46;
            margin-bottom: 0;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-left: auto;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-input {
            width: 320px;
            padding: 12px 20px 12px 45px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            font-size: 14px;
            color: #374151;
            outline: none;
            transition: all 0.2s ease;
        }
        
        .search-input:focus {
            background: rgba(255, 255, 255, 0.8);
            border-color: rgba(6, 95, 70, 0.5);
            box-shadow: 0 0 0 3px rgba(6, 95, 70, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }
        
        .notification-icon {
            position: relative;
            padding: 10px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 12px;
            color: #6b7280;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .notification-icon:hover {
            background: rgba(255, 255, 255, 0.8);
            color: #065f46;
        }
        
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .user-profile:hover {
            background: rgba(255, 255, 255, 0.8);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #065f46;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: #065f46;
            line-height: 1;
        }
        
        .user-role {
            font-size: 12px;
            color: #6b7280;
            line-height: 1;
        }
        
        .content-area {
            flex: 1;
            padding: 30px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        /* Responsive Design */
        .mobile-sidebar-toggle {
            display: none;
        }
        
        @media (max-width: 1024px) {
            .main-container {
                padding: 15px;
                gap: 15px;
            }
            
            .sidebar {
                width: 250px;
            }
            
            .search-input {
                width: 250px;
            }
            
            .page-title {
                font-size: 28px;
            }
        }
        
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                padding: 10px;
                gap: 10px;
            }
            
            .sidebar {
                width: 100%;
                position: fixed;
                top: 0;
                left: -100%;
                height: 100vh;
                z-index: 1000;
                overflow-y: auto;
                transition: left 0.3s ease;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .sidebar-close-btn {
                display: block;
            }
            
            .main-content {
                max-width: 100%;
            }
            
            .mobile-sidebar-toggle {
                display: block;
                padding: 10px;
                background: rgba(255, 255, 255, 0.6);
                border: none;
                border-radius: 8px;
                color: #065f46;
                font-size: 18px;
                cursor: pointer;
            }
            
            .top-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .header-actions {
                width: 100%;
                justify-content: space-between;
                margin-left: 0;
            }
            
            .search-input {
                width: 200px;
                font-size: 14px;
            }
            
            .user-profile {
                padding: 6px 12px;
            }
            
            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }
            
            .content-area {
                padding: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .search-input {
                width: 150px;
            }
            
            .page-title {
                font-size: 24px;
            }
            
            .content-area {
                padding: 15px;
            }
            
            .user-info {
                display: none;
            }
        }
        
        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .slide-in-left {
            animation: slideInLeft 0.3s ease-out;
        }
        
        @keyframes slideInLeft {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    @auth
        <div class="main-container">
            <!-- Sidebar -->
            <aside class="sidebar slide-in-left" id="sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="Credai">
                    </div>
                    <button class="sidebar-close-btn" onclick="closeSidebar()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <nav>
                    <ul class="nav-menu">
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-home"></i>
                                    Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.volunteers') }}" class="nav-link {{ request()->routeIs('admin.volunteers') ? 'active' : '' }}">
                                    <i class="fas fa-users"></i>
                                    Volunteers
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('trees.index') }}" class="nav-link {{ request()->routeIs('trees.*') ? 'active' : '' }}">
                                <i class="fas fa-tree"></i>
                                My Trees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('inspections.upcoming') }}" class="nav-link {{ request()->routeIs('inspections.*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-check"></i>
                                Inspections
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                    
                    <div class="nav-divider"></div>
                    
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-user-plus"></i>
                                Add Account
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-exchange-alt"></i>
                                Switch Account
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" onclick="performLogout(event)">
                                <i class="fas fa-sign-out-alt"></i>
                                Log Out
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>
            
            <!-- Main Content -->
            <main class="main-content fade-in">
                <div class="top-header">
                    <button class="mobile-sidebar-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <h1 class="page-title">
                        @if(request()->routeIs('admin.dashboard'))
                            Admin Dashboard
                        @elseif(request()->routeIs('admin.volunteers'))
                            Volunteers
                        @elseif(request()->routeIs('trees.*'))
                            My Trees
                        @elseif(request()->routeIs('inspections.*'))
                            Inspections
                        @else
                            Dashboard
                        @endif
                    </h1>
                    
                    <div class="header-actions">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" placeholder="Search Here">
                        </div>
                        
                        <div class="notification-icon">
                            <i class="fas fa-bell"></i>
                            <div class="notification-badge"></div>
                        </div>
                        
                        <div class="user-profile">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ Auth::user()->name }}</div>
                                <div class="user-role">
                                    {{ auth()->user()->isAdmin() ? 'Administrator' : 'Volunteer' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="content-area">
                    @yield('content')
                </div>
            </main>
        </div>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    @else
        @yield('content')
    @endauth

    <script>
        function performLogout(event) {
            event.preventDefault();
            
            const logoutForm = document.getElementById('logout-form');
            if (logoutForm) {
                logoutForm.submit();
            } else {
                console.warn('Logout form not found, using fallback method');
                window.location.href = '{{ route("logout") }}';
            }
        }
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.remove('active');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.mobile-sidebar-toggle');
            
            if (window.innerWidth <= 768 && 
                sidebar.classList.contains('active') && 
                !sidebar.contains(event.target) && 
                !toggleBtn.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>
