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
        .carousel button{
            position: absolute !important;
            padding: 0 !important;
        } 
        .carousel-indicators button{
            position: relative !important;
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
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #6b7280;
            font-size: 18px;
            cursor: pointer;
            padding: 10px;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-close-btn:hover {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            transform: rotate(90deg) scale(1.1);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
        }
        
        .sidebar-close-btn:active {
            transform: rotate(90deg) scale(0.95);
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
            padding: 12px;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 14px;
            color: #6b7280;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .notification-icon:hover {
            background: rgba(255, 255, 255, 0.8);
            color: #065f46;
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(6, 95, 70, 0.15);
        }
        
        .notification-icon:active {
            transform: translateY(0) scale(0.95);
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
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(31, 38, 135, 0.08);
        }
        
        .user-profile:hover {
            background: rgba(255, 255, 255, 0.8);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(6, 95, 70, 0.12);
            border-color: rgba(6, 95, 70, 0.2);
        }
        
        .user-profile:active {
            transform: translateY(0);
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
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            color: #065f46;
            font-size: 18px;
            cursor: pointer;
            padding: 12px 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(31, 38, 135, 0.1);
        }
        
        .mobile-sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.85);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 95, 70, 0.2);
        }
        
        .mobile-sidebar-toggle:active {
            transform: translateY(0);
            box-shadow: 0 4px 16px rgba(31, 38, 135, 0.1);
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
                display: flex;
            }
            
            .main-content {
                max-width: 100%;
            }
            
            .mobile-sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .top-header {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 15px;
                align-items: center;
            }
            
            .page-title {
                font-size: 24px;
                flex: 1;
                min-width: 0;
            }
            
            .header-actions {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-shrink: 0;
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
            .main-container {
                padding: 8px;
                gap: 8px;
            }
            
            .top-header {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }
            
            .page-title {
                font-size: 22px;
                text-align: center;
            }
            
            .header-actions {
                justify-content: center;
                flex-wrap: wrap;
                gap: 8px;
            }
            
            .search-input {
                width: 180px;
                padding: 10px 16px 10px 38px;
            }
            
            .notification-icon {
                width: 40px;
                height: 40px;
                padding: 10px;
            }
            
            .user-profile {
                padding: 6px 12px;
            }
            
            .user-info {
                display: none;
            }
            
            .content-area {
                padding: 15px;
                border-radius: 16px;
            }
            
            .sidebar-header {
                margin-bottom: 30px;
                padding-bottom: 25px;
            }
            
            .sidebar-logo img {
                height: 50px;
            }
            
            .sidebar-close-btn {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }
        }
        
        /* Modern Button Styling */
        .btn, button:not(.sidebar-close-btn):not(.mobile-sidebar-toggle):not(.notification-icon) {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            text-decoration: none;
        }
        
        .btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Primary Button */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }
        
        /* Success Button */
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .btn-success:hover, .btn-success:focus {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }
        
        /* Warning Button */
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        
        .btn-warning:hover, .btn-warning:focus {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
        }
        
        /* Danger Button */
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .btn-danger:hover, .btn-danger:focus {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }
        
        /* Info Button */
        .btn-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }
        
        .btn-info:hover, .btn-info:focus {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.3);
        }
        
        /* Secondary Button */
        .btn-secondary {
            background: rgba(255, 255, 255, 0.8);
            color: #374151;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(15px);
        }
        
        .btn-secondary:hover, .btn-secondary:focus {
            background: rgba(255, 255, 255, 0.9);
            color: #374151;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        /* Outline Buttons */
        .btn-outline-primary {
            background: rgba(255, 255, 255, 0.8);
            color: #3b82f6;
            border: 2px solid #3b82f6;
            backdrop-filter: blur(10px);
        }
        
        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .btn-outline-success {
            background: rgba(255, 255, 255, 0.8);
            color: #10b981;
            border: 2px solid #10b981;
            backdrop-filter: blur(10px);
        }
        
        .btn-outline-success:hover, .btn-outline-success:focus {
            background: #10b981;
            color: white;
            border-color: #10b981;
        }
        
        .btn-outline-warning {
            background: rgba(255, 255, 255, 0.8);
            color: #f59e0b;
            border: 2px solid #f59e0b;
            backdrop-filter: blur(10px);
        }
        
        .btn-outline-warning:hover, .btn-outline-warning:focus {
            background: #f59e0b;
            color: white;
            border-color: #f59e0b;
        }
        
        /* Small Button */
        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
            border-radius: 10px;
        }
        
        /* Large Button */
        .btn-lg {
            padding: 16px 32px;
            font-size: 16px;
            border-radius: 14px;
        }
        
        /* Nav Pills/Tabs */
        .nav-tabs .nav-link {
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #6b7280;
            border-radius: 12px 12px 0 0;
            margin-right: 4px;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .nav-tabs .nav-link:hover {
            background: rgba(255, 255, 255, 0.8);
            color: #065f46;
        }
        
        .nav-tabs .nav-link.active {
            background: rgba(255, 255, 255, 0.9);
            color: #065f46;
            border-bottom-color: transparent;
        }
        
        /* Cards */
        .card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
        }
        
        .card-header {
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px 16px 0 0;
            padding: 20px;
            font-weight: 600;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .card-footer {
            background: rgba(255, 255, 255, 0.05);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0 0 16px 16px;
            padding: 15px 20px;
        }
        
        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            backdrop-filter: blur(10px);
        }
        
        /* Breadcrumbs */
        .breadcrumb {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 12px 16px;
        }
        
        .breadcrumb-item a {
            color: #065f46;
            text-decoration: none;
            font-weight: 500;
        }
        
        /* Alerts */
        .alert {
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.2);
            color: #047857;
        }
        
        /* Form Controls */
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        /* Button Group Styling */
        .btn-group, .btn-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        
        .btn-group .btn {
            margin: 0;
        }
        
        /* Header Button Container */
        .header-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
        }
        
        /* Action Button Container */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        
        /* Card Action Buttons */
        .card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        
        /* Responsive Button Adjustments */
        @media (max-width: 768px) {
            .btn {
                padding: 10px 16px;
                font-size: 13px;
                min-width: 120px;
                text-align: center;
            }
            
            .btn-sm {
                padding: 8px 12px;
                font-size: 12px;
                min-width: 100px;
            }
            
            .btn-lg {
                padding: 12px 24px;
                font-size: 14px;
                min-width: 140px;
            }
            
            /* Make button containers stack properly on mobile */
            .header-buttons {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                width: 100%;
            }
            
            .header-buttons .btn {
                width: 100%;
                justify-content: center;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                width: 100%;
            }
            
            .action-buttons .btn {
                width: 100%;
                justify-content: center;
            }
            
            .card-actions {
                flex-direction: column;
                gap: 6px;
            }
            
            .card-actions .btn {
                width: 100%;
                justify-content: center;
            }
            
            /* Fix breadcrumb and header alignment */
            .d-flex.justify-content-between {
                flex-direction: column !important;
                gap: 16px;
                align-items: stretch !important;
            }
            
            .d-flex.justify-content-between > div:last-child {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            
            /* Tab navigation responsive */
            .nav-tabs {
                flex-wrap: wrap;
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .nav-tabs .nav-link {
                margin-right: 0;
                margin-bottom: 4px;
                border-radius: 12px;
                flex: 1;
                text-align: center;
                min-width: 0;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            /* Card footer buttons */
            .card-footer {
                padding: 12px 16px;
            }
            
            .card-footer .btn {
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .btn {
                padding: 10px 14px;
                font-size: 12px;
                min-width: 100px;
                gap: 6px;
            }
            
            .btn-sm {
                padding: 8px 10px;
                font-size: 11px;
                min-width: 80px;
            }
            
            .btn i {
                font-size: 14px;
            }
            
            .btn-sm i {
                font-size: 12px;
            }
            
            /* Ultra mobile - hide text on some buttons, keep icons */
            .btn-mobile-icon-only {
                min-width: 44px;
                padding: 10px;
            }
            
            .btn-mobile-icon-only .btn-text {
                display: none;
            }
            
            /* Stack all buttons vertically on very small screens */
            .header-buttons .btn,
            .action-buttons .btn,
            .card-actions .btn {
                width: 100%;
                margin-bottom: 4px;
            }
            
            /* Compact nav tabs */
            .nav-tabs .nav-link {
                padding: 10px 8px;
                font-size: 11px;
            }
            
            .nav-tabs .nav-link i {
                display: block;
                margin-bottom: 4px;
                font-size: 16px;
            }
        }
        
        /* Utility Classes for Button Layout */
        .btn-stack-mobile {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        @media (max-width: 768px) {
            .btn-stack-mobile {
                flex-direction: column;
            }
            
            .btn-stack-mobile .btn {
                width: 100%;
            }
        }
        
        .btn-row-mobile {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        @media (max-width: 768px) {
            .btn-row-mobile {
                justify-content: center;
            }
            
            .btn-row-mobile .btn {
                flex: 1;
                min-width: 0;
            }
        }
        
        /* Carousel Controls - Preserve Original Bootstrap Style */
        .carousel-control-prev,
        .carousel-control-next {
            background: rgba(0, 0, 0, 0.5) !important;
            border: none !important;
            border-radius: 0 !important;
            backdrop-filter: none !important;
            box-shadow: none !important;
            width: 15% !important;
            height: 100% !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: background-color 0.15s ease !important;
        }
        
        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            background: rgba(0, 0, 0, 0.7) !important;
            transform: none !important;
            box-shadow: none !important;
        }
        
        .carousel-control-prev:active,
        .carousel-control-next:active {
            transform: none !important;
            box-shadow: none !important;
        }
        
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-image: none !important;
            width: 2rem !important;
            height: 2rem !important;
            background-color: transparent !important;
        }
        
        .carousel-control-prev-icon::before {
            content: '<';
            font-size: 2rem;
            color: white;
            font-weight: bold;
            line-height: 1;
        }
        
        .carousel-control-next-icon::before {
            content: '>';
            font-size: 2rem;
            color: white;
            font-weight: bold;
            line-height: 1;
        }
        
        /* Carousel Indicators */
        .carousel-indicators {
            bottom: 1rem !important;
            margin-bottom: 0 !important;
            margin-left: 15% !important;
            margin-right: 15% !important;
        }
        
        .carousel-indicators [data-bs-target] {
            background-color: rgba(255, 255, 255, 0.5) !important;
            border: none !important;
            border-radius: 50% !important;
            width: 12px !important;
            height: 12px !important;
            margin: 0 3px !important;
            cursor: pointer !important;
            transition: background-color 0.15s ease !important;
        }
        
        .carousel-indicators [data-bs-target].active {
            background-color: white !important;
        }
        
        .carousel-indicators [data-bs-target]:hover {
            background-color: rgba(255, 255, 255, 0.8) !important;
        }
        
        /* Carousel Images */
        .carousel-item img {
            border-radius: 8px !important;
        }
        
        /* Override any button styles that might affect carousel controls */
        .carousel-control-prev,
        .carousel-control-next,
        .carousel-indicators [data-bs-target] {
            backdrop-filter: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        
        .carousel-control-prev:not(.btn),
        .carousel-control-next:not(.btn) {
            padding: 0 !important;
            min-width: auto !important;
            gap: 0 !important;
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
                        {{-- <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                        </li> --}}
                    </ul>
                    
                    <div class="nav-divider"></div>
                    
                    <ul class="nav-menu">
                        {{-- <li class="nav-item">
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
                        </li> --}}
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
