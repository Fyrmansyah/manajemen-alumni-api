<!-- HEADER -->
<header id="header" class="header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3">
                <div class="logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/images/logo BKK.png') }}" alt="BKK SMK Negeri 1 Surabaya" class="hero-logo">
                    </a>
                </div>
            </div>
            
            <div class="col-md-9">
                <nav class="navigation">
                    <div class="open-menu" onclick="toggleMobileMenu()">
                        <span class="item item-1"></span>
                        <span class="item item-2"></span>
                        <span class="item item-3"></span>
                    </div>

                    <ul class="menu">
                        <li class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                            <a href="{{ route('home') }}">Beranda</a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('jobs.*') ? 'active' : '' }}">
                            <a href="{{ route('jobs.index') }}">Lowongan Kerja</a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('news.*') ? 'active' : '' }}">
                            <a href="{{ route('news.index') }}">Berita</a>
                        </li>
                        
                        @if(Auth::guard('admin')->check())
                            <li class="menu-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); performLogout('admin-logout-form');">
                                    Logout ({{ Auth::guard('admin')->user()->nama ?? Auth::guard('admin')->user()->username }})
                                </a>
                                <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @elseif(Auth::guard('alumni')->check())
                            <li class="menu-item">
                                <a href="{{ route('alumni.dashboard') }}">Dashboard Alumni</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); performLogout('alumni-logout-form');">
                                    Logout ({{ Auth::guard('alumni')->user()->nama ?? Auth::guard('alumni')->user()->email }})
                                </a>
                                <form id="alumni-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @elseif(Auth::guard('company')->check())
                            <li class="menu-item">
                                <a href="{{ route('company.dashboard') }}">Dashboard Perusahaan</a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); performLogout('company-logout-form');">
                                    Logout ({{ Auth::guard('company')->user()->nama ?? Auth::guard('company')->user()->email }})
                                </a>
                                <form id="company-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @else
                            <li class="menu-item {{ request()->routeIs('login') ? 'active' : '' }}">
                                <a href="{{ route('login') }}">Login</a>
                            </li>
                            <li class="menu-item dropdown {{ request()->routeIs('register') || request()->routeIs('company.register') ? 'active' : '' }}">
                                <a href="#" class="dropdown-toggle">Daftar</a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">Daftar untuk Alumni</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('company.register') ? 'active' : '' }}" href="{{ route('company.register') }}">Daftar untuk Perusahaan</a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>
<!-- END / HEADER -->

<!-- Breadcrumb Section -->
@if (isset($show_breadcrumb) && $show_breadcrumb)
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Beranda</a>
                </li>
                @if (isset($breadcrumb_items))
                    @foreach ($breadcrumb_items as $item)
                        @if (isset($item['url']))
                            <li class="breadcrumb-item">
                                <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                            </li>
                        @else
                            <li class="breadcrumb-item active" aria-current="page">{{ $item['title'] }}</li>
                        @endif
                    @endforeach
                @endif
            </ol>
        </nav>
    </div>
@endif


<script>
    function toggleMobileMenu() {
        const menu = document.querySelector('.menu');
        const openMenu = document.querySelector('.open-menu');
        
        menu.classList.toggle('active');
        openMenu.classList.toggle('active');
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        const menu = document.querySelector('.menu');
        const openMenu = document.querySelector('.open-menu');
        
        // Close mobile menu if clicked outside
        if (!menu.contains(e.target) && !openMenu.contains(e.target)) {
            menu.classList.remove('active');
            openMenu.classList.remove('active');
        }
    });

    // Close mobile menu when clicking menu items
    document.querySelectorAll('.menu-item a:not(.dropdown-toggle), .dropdown-menu a').forEach(link => {
        link.addEventListener('click', function() {
            const menu = document.querySelector('.menu');
            const openMenu = document.querySelector('.open-menu');
            
            if (window.innerWidth <= 768) {
                menu.classList.remove('active');
                openMenu.classList.remove('active');
            }
        });
    });
</script>
