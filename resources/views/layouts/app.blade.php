<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Lamaran Kerja BKK SMKN 1 Surabaya')</title>
    
    <link rel="icon" type="image/png" sizes="96x96" href="https://www.smkn1-sby.sch.id/assets/template/landing/images/favicon.png">
    
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,700,900" rel='stylesheet' type='text/css'>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Custom CSS for Sistem Lamaran Kerja BKK SMKN 1 Surabaya */

        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
        }

        /* Reset dan Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Lato', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }

        .main-content {
            min-height: calc(100vh - 200px);
            padding: 0;
        }

        /* Header Styles */
        .header {
            background: #fff;
            padding: 15px 0;
            position: relative;
            z-index: 1000;
        }

        .logo img {
            height: 60px;
            width: auto;
        }

        /* Navigation Styles */
        .navigation {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
        }

        .menu-item {
            margin: 0 5px;
            position: relative;
        }

        .menu-item a {
            display: block;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        
        .menu-item.active a {
            color: #37abf2;
        }
        
        a.dropdown-item:not(:active) {
            color: #333;
        }

        /* Dropdown Styles */
        .dropdown-menu {
            display: none;
            position: absolute;
            background: #fff;
            min-width: 200px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 5px;
            z-index: 1000;
            top: 100%;
            left: 0;
        }

        .dropdown-item.active, 
        .dropdown-item:active {
            background-color: #e9eaeb5a;
        }

        .menu-item.dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu .dropdown-item {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            font-weight: 400;
        }

        .dropdown-menu .dropdown-item:hover {
            background: #f8f9fa;
        }

        .dropdown-menu .dropdown-item.active {
            color: #37abf2;
        }

        /* Mobile Menu Toggle */
        .open-menu {
            position: absolute;
            width: 50px;
            height: 14px;
            cursor: pointer;
            margin: auto;
            top: 0;
            right: 15px;
            bottom: 0;
            z-index: 999;
            display: none;
        }

        .open-menu .item {
            position: absolute;
            display: block;
            font-size: 0;
            width: 20px;
            height: 2px;
            background-color: #012340;
            margin: auto;
            left: 0;
            right: 0;
            overflow: hidden;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .open-menu .item-1 {
            top: 0;
        }

        .open-menu .item-2 {
            top: 0;
            bottom: 0;
        }

        .open-menu .item-3 {
            bottom: 0;
        }

        .open-menu:hover .item,
        .open-menu.active .item {
            background-color: #37abf2;
        }

        .open-menu.active .item-1 {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .open-menu.active .item-2 {
            opacity: 0;
        }

        .open-menu.active .item-3 {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .open-menu {
                display: block;
            }

            .navigation {
                text-align: center;
            }

            .menu {
                position: fixed;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100vh;
                background: #fff;
                flex-direction: column;
                justify-content: flex-start;
                padding: 80px 20px 20px;
                z-index: 999;
                overflow-y: auto;
                transition: left 0.3s ease;
                font-size: 16px;
                display: flex;
            }

            .menu.active {
                left: 0;
            }

            .menu-item {
                margin: 10px 0;
                width: 100%;
                border-bottom: 1px solid #eee;
                display: block;
            }

            .menu-item a {
                padding: 15px 0;
                width: 100%;
                text-align: left;
                line-height: 1.5;
                display: block;
            }

            .dropdown-menu,
            .sub-menu {
                position: static;
                background: #f8f9fa;
                box-shadow: none;
                border: none;
                padding: 10px 0;
                display: block;
                border-radius: 0;
                margin-top: 10px;
                opacity: 1;
                visibility: visible;
                width: 100%;
            }

            .dropdown-menu li,
            .sub-menu li {
                border-top: 1px solid #e4e4e4;
            }

            .dropdown-menu a,
            .sub-menu a {
                padding: 10px 20px;
                line-height: 1.5;
            }
        }
        }

        /* Breadcrumb Styles */
        .breadcrumb {
            background: transparent;
            padding: 8px 0;
            margin: 0;
        }

        .breadcrumb-item {
            font-size: 14px;
        }

        .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #333;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .card-body {
            padding: 25px;
        }

        .card-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .card-text {
            color: #7f8c8d;
            line-height: 1.7;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 12px 25px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: #37ABF2;
            border: none;
            color: #fff;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #1a6fa3, #1a6fa3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(69, 177, 255, 0.905);
        }

        .btn-outline-primary {
            border: 2px solid #37ABF2;
            color: #37ABF2;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: #37ABF2;
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-lg {
            padding: 15px 35px;
            font-size: 1.1rem;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.875rem;
        }

        /* Footer Styling */
        .footer {
            background-color: #175690;
            color: white;
            font-family: 'Lato', sans-serif;
            font-size: 14px;
            line-height: 1.4;
            margin-top: 50px;
        }

        .footer .first-footer {
            padding: 40px 0;
        }

        .footer .second-footer {
            background-color: #000;
            padding: 20px 0;
        }

        .footer h3 {
            color: white;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .footer p {
            color: white;
            margin-bottom: 15px;
        }

        .footer ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer .list-style-block li {
            margin-bottom: 10px;
        }

        .footer .list-style-block li a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer .list-style-block li a:hover {
            color: #ccc;
        }

        .footer .contact {
            text-align: left;
            color: #666;
        }

        .footer .copyright {
            text-align: right;
            color: #666;
            margin: 0;
            padding-top: 5px;
        }

        .footer .email a {
            color: #666;
            text-decoration: none;
        }

        .footer iframe {
            border-radius: 8px;
        }

        /* Social Media Links */
        .footer .widget ul[style*="display: inline-flex"] {
            display: flex !important;
            gap: 8px;
            list-style: none;
            padding: 0;
            margin: 25px 0 0 0;
        }

        .footer .widget ul[style*="display: inline-flex"] li {
            margin: 0;
        }

        .footer .widget ul[style*="display: inline-flex"] li a {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: 1px solid #ffffff;
            border-radius: 4px;
            color: #ffffff;
            text-decoration: none;
            margin: 5px;
            padding: 10px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .footer .widget ul[style*="display: inline-flex"] li a:hover {
            background: #ffffff;
            color: #175690;
        }

        /* Animation Classes */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Enhancements */
        @media (max-width: 768px) {
            .footer .copyright {
                text-align: center;
                margin-top: 15px;
            }

            .footer .contact {
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 15px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 0.875rem;
            }
        }

        /* Focus States */
        .btn:focus,
        .card:focus,
        a:focus {
            outline: 2px solid #37ABF2;
            outline-offset: 2px;
        }

        /* Global Pagination Styles */
        .pagination {
            margin: 0;
            justify-content: center;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 8px;
            color: #37ABF2;
            border: 1px solid #dee2e6;
            margin: 0 2px;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            color: #fff;
            background-color: #37ABF2;
            border-color: #37ABF2;
            transform: translateY(-1px);
        }

        .pagination .page-item.active .page-link {
            background-color: #37ABF2;
            border-color: #37ABF2;
            color: #fff;
            box-shadow: 0 4px 8px rgba(55, 171, 242, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
            cursor: not-allowed;
        }

        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            background-color: #fff;
            color: #6c757d;
        }

        /* Arrow icons styling */
        .pagination .page-link i {
            font-size: 0.875rem;
            line-height: 1;
        }

        /* Better spacing */
        .pagination .page-item {
            margin: 0 1px;
        }
    </style>
    
    @yield('styles')
    @stack('head-scripts')
</head>
<body>
    <!-- Include Header Component -->
    @include('layouts.header')

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Include Footer Component -->
    @include('layouts.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Logout Helper Script -->
    <script>
        function performLogout(formId) {
            try {
                const form = document.getElementById(formId);
                if (form) {
                    form.submit();
                } else {
                    // Fallback: redirect to login page
                    window.location.href = '{{ route("login") }}';
                }
            } catch (error) {
                console.error('Logout error:', error);
                // Fallback: redirect to login page
                window.location.href = '{{ route("login") }}';
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
