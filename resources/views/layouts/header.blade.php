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
                    <div class="open-menu">
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
                            
                            <!-- Notification Bell -->
                            <li class="menu-item dropdown notification-dropdown">
                                <a href="#" class="dropdown-toggle notification-bell" id="notificationBell">
                                    <i class="fas fa-bell"></i>
                                    <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                                </a>
                                <ul class="dropdown-menu notification-menu" id="notificationMenu">
                                    <li class="notification-header">
                                        <h6>Notifikasi <span class="text-muted" id="notificationCount">(0)</span></h6>
                                        <button class="btn btn-sm btn-link mark-all-read" id="markAllRead">Tandai Semua Dibaca</button>
                                    </li>
                                    <li class="notification-items" id="notificationItems">
                                        <div class="text-center py-3 text-muted">Tidak ada notifikasi</div>
                                    </li>
                                    <li class="notification-footer">
                                        <a href="/admin/notifications" class="btn btn-sm btn-primary w-100">Lihat Semua Notifikasi</a>
                                    </li>
                                </ul>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality
    const hamburger = document.querySelector('.open-menu');
    const mobileMenu = document.querySelector('.navigation .menu');
    
    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });
        
        // Close menu when clicking overlay or outside
        document.addEventListener('click', function(e) {
            if (mobileMenu.classList.contains('active') && 
                !mobileMenu.contains(e.target) && 
                !hamburger.contains(e.target)) {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        });
        
        // Close menu when window is resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        });
    }
});
</script>
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
    function performLogout(formId){
        const form = document.getElementById(formId);
        if(form){ form.submit(); }
    }
</script>

@if(Auth::guard('admin')->check())
<style>
/* Navigation container styles to prevent clipping */
.navigation {
    overflow: visible !important;
}

.navigation .menu {
    overflow: visible !important;
    display: flex;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
}

.navigation .menu-item {
    overflow: visible !important;
    position: relative;
}

/* Hamburger Menu Styles */
.open-menu {
    display: none;
    flex-direction: column;
    justify-content: space-around;
    width: 30px;
    height: 25px;
    cursor: pointer;
    padding: 0;
    border: none;
    background: transparent;
    z-index: 1002;
}

/* Show hamburger only on mobile */
@media (max-width: 768px) {
    .open-menu {
        display: flex;
    }
}

.open-menu .item {
    width: 100%;
    height: 3px;
    background: #333;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.open-menu.active .item:nth-child(1) {
    transform: rotate(45deg) translate(7px, 7px);
}

.open-menu.active .item:nth-child(2) {
    opacity: 0;
}

.open-menu.active .item:nth-child(3) {
    transform: rotate(-45deg) translate(8px, -8px);
}

/* Notification Bell Styles */
.notification-dropdown {
    position: relative;
    z-index: 1001;
    overflow: visible !important;
}

.notification-bell {
    position: relative;
    display: flex !important;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #f8f9fa;
    color: #666;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    margin: 0 8px;
    text-decoration: none !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    font-size: 18px;
}

.notification-bell:hover {
    background: #e9ecef;
    color: #333;
    border-color: #007bff;
    transform: scale(1.05);
    text-decoration: none !important;
}

.notification-bell.has-notifications {
    color: #007bff;
    background: #e3f2fd;
}

.notification-bell.has-notifications:hover {
    background: #bbdefb;
}

.notification-badge {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #dc3545;
    color: white;
    border-radius: 12px;
    padding: 4px 8px;
    font-size: 12px;
    font-weight: bold;
    min-width: 22px;
    text-align: center;
    line-height: 1.2;
    animation: pulse 2s infinite;
    border: 2px solid white;
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    z-index: 1002;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.notification-menu {
    min-width: 350px;
    max-width: 420px;
    border: 1px solid #e0e6ed;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.12), 0 4px 8px rgba(0,0,0,0.08);
    padding: 0;
    margin-top: 10px;
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    z-index: 1000;
    transform: translateX(-85%); /* Move 85% of the dropdown width to the left */
    backdrop-filter: blur(10px);
}

.notification-menu.show {
    display: block;
    animation: slideInDown 0.3s ease;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateX(-85%) translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(-85%) translateY(0);
    }
}

.notification-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #37ABF2 0%, #4facfe 100%);
    color: white;
    border-radius: 12px 12px 0 0;
}

.notification-header h6 {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    color: white;
}

.mark-all-read {
    font-size: 12px;
    padding: 6px 12px;
    color: rgba(255,255,255,0.9);
    text-decoration: none;
    background: rgba(255,255,255,0.1);
    border-radius: 6px;
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 0.2s ease;
}

.mark-all-read:hover {
    background: rgba(255,255,255,0.2);
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

.notification-items {
    max-height: 320px;
    overflow-y: auto;
    padding: 0;
}

.notification-items::-webkit-scrollbar {
    width: 6px;
}

.notification-items::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-items::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.notification-items::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    padding: 14px 20px;
    border-bottom: 1px solid #f5f5f5;
    color: #333;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
    gap: 12px;
}

.notification-item:hover {
    background: #f8fafe;
    color: #333;
    transform: translateX(2px);
}

.notification-item.unread {
    background: linear-gradient(135deg, #fff9e6 0%, #fffbf0 100%);
    border-left: 4px solid #ffc107;
    position: relative;
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    top: 50%;
    right: 20px;
    width: 8px;
    height: 8px;
    background: #ffc107;
    border-radius: 50%;
    transform: translateY(-50%);
    animation: pulse 2s infinite;
}

.notification-item-icon {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #37ABF2 0%, #4facfe 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    margin-right: 0;
    box-shadow: 0 2px 8px rgba(55, 171, 242, 0.3);
}

.notification-item-content {
    flex: 1;
    min-width: 0; /* Allow text to wrap properly */
}

.notification-item-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
    line-height: 1.4;
    color: #2c3e50;
    word-wrap: break-word;
}

.notification-item-message {
    font-size: 13px;
    color: #6c757d;
    line-height: 1.4;
    margin-bottom: 6px;
    word-wrap: break-word;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notification-item-time {
    font-size: 11px;
    color: #9ca3af;
    font-weight: 500;
}

.notification-footer {
    padding: 16px 20px;
    border-top: 1px solid #e9ecef;
    background: #f8fafe;
    border-radius: 0 0 12px 12px;
}

.notification-footer .btn {
    width: 100%;
    padding: 12px 16px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    background: linear-gradient(135deg, #37ABF2 0%, #4facfe 100%);
    border: none;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(55, 171, 242, 0.3);
}

.notification-footer .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(55, 171, 242, 0.4);
}

/* Remove the old hover behavior and add click behavior */
.notification-dropdown .notification-menu {
    display: none;
}

.notification-dropdown .notification-menu.show {
    display: block;
}

/* Mobile responsive */
@media (max-width: 768px) {
    /* Hide navigation menu by default on mobile */
    .navigation .menu {
        position: fixed;
        top: 0;
        left: -100%;
        width: 280px;
        height: 100vh;
        background: #fff;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        transition: left 0.3s ease;
        flex-direction: column;
        padding: 80px 20px 20px;
        z-index: 1001;
        overflow-y: auto;
    }
    
    /* Show menu when active */
    .navigation .menu.active {
        left: 0;
    }
    
    /* Mobile menu items */
    .navigation .menu .menu-item {
        width: 100%;
        margin: 0 0 10px 0;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .navigation .menu .menu-item a {
        padding: 15px 0;
        display: block;
        color: #333;
        text-decoration: none;
        font-size: 16px;
    }
    
    .navigation .menu .menu-item a:hover,
    .navigation .menu .menu-item.active a {
        color: #37ABF2;
    }
    
    /* Mobile dropdown menus */
    .navigation .menu .dropdown-menu {
        position: static;
        display: none;
        box-shadow: none;
        border: none;
        padding-left: 20px;
        background: #f8f9fa;
        margin-top: 10px;
        border-radius: 8px;
    }
    
    .navigation .menu .dropdown:hover .dropdown-menu {
        display: block;
    }
    
    /* Overlay for mobile menu */
    .navigation .menu.active::before {
        content: '';
        position: fixed;
        top: 0;
        left: 280px;
        width: calc(100vw - 280px);
        height: 100vh;
        background: rgba(0,0,0,0.5);
        z-index: -1;
    }
    
    /* MOBILE NOTIFICATION - Hide dropdown completely and make bell a link */
    .notification-menu {
        display: none !important;
    }
    
    .notification-dropdown {
        position: relative;
    }
    
    .notification-bell {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(55, 171, 242, 0.1);
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
        position: relative;
    }
    
    .notification-bell:hover {
        background: rgba(55, 171, 242, 0.2);
        transform: scale(1.05);
        text-decoration: none;
        color: inherit;
    }
    
    .notification-bell i {
        font-size: 20px;
        color: #37ABF2;
    }
    
    /* Mobile notification badge - prominent red circle with number */
    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ff4757;
        color: white;
        border-radius: 50%;
        padding: 0;
        font-size: 12px;
        font-weight: 700;
        min-width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid white;
        box-shadow: 0 3px 8px rgba(255, 71, 87, 0.4);
        animation: pulse 2s infinite;
        line-height: 1;
    }
    
    /* Enhanced pulse animation for mobile */
    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 3px 8px rgba(255, 71, 87, 0.4);
        }
        50% {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.6);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 3px 8px rgba(255, 71, 87, 0.4);
        }
    }
    
    /* Hide notification badge when count is 0 */
    .notification-badge[style*="display: none"] {
        display: none !important;
    }
    
    /* Notification bell with active state */
    .notification-bell.has-notifications {
        background: rgba(55, 171, 242, 0.15);
        animation: bellRing 3s ease-in-out infinite;
    }
    
    @keyframes bellRing {
        0%, 50%, 100% { transform: rotate(0deg); }
        10%, 30% { transform: rotate(-10deg); }
        20%, 40% { transform: rotate(10deg); }
    }
}
}

/* Desktop responsive - prevent going too far left on very wide screens */
@media (min-width: 1200px) {
    .notification-menu {
        transform: translateX(-90%); /* Move even more to the left on larger screens */
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notification functionality
    const notificationBell = document.getElementById('notificationBell');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationCount = document.getElementById('notificationCount');
    const notificationItems = document.getElementById('notificationItems');
    const notificationMenu = document.getElementById('notificationMenu');
    const markAllRead = document.getElementById('markAllRead');
    
    let notificationUpdateInterval;
    
    // Load notifications on page load
    loadNotifications();
    
    // Update notifications every 30 seconds
    notificationUpdateInterval = setInterval(loadNotifications, 30000);
    
    // Toggle notification dropdown on click
    if (notificationBell) {
        notificationBell.addEventListener('click', function(e) {
            // Check if we're on mobile
            if (window.innerWidth <= 768) {
                // On mobile, redirect to notifications page instead of showing dropdown
                window.location.href = '/admin/notifications';
                return;
            }
            
            // Desktop behavior - show dropdown
            e.preventDefault();
            e.stopPropagation();
            
            if (notificationMenu.classList.contains('show')) {
                notificationMenu.classList.remove('show');
            } else {
                // Close any other open dropdowns first
                document.querySelectorAll('.notification-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
                notificationMenu.classList.add('show');
                // Refresh notifications when opening
                loadNotifications();
            }
        });
    }
    
    // Close dropdown when clicking outside (desktop only)
    document.addEventListener('click', function(e) {
        // Only handle dropdown closing on desktop
        if (window.innerWidth > 768) {
            const dropdown = document.querySelector('.notification-dropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                notificationMenu.classList.remove('show');
            }
        }
    });
    
    // Prevent dropdown from closing when clicking inside it (desktop only)
    if (notificationMenu) {
        notificationMenu.addEventListener('click', function(e) {
            if (window.innerWidth > 768) {
                e.stopPropagation();
            }
        });
    }
    
    // Handle window resize - close dropdown when switching to mobile
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768 && notificationMenu.classList.contains('show')) {
            notificationMenu.classList.remove('show');
        }
    });
    
    // Load recent notifications
    function loadNotifications() {
        fetch('/admin/notifications/recent?limit=10')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationDropdown(data.data);
                    const unreadCount = data.data.filter(n => !n.is_read).length;
                    updateNotificationBadge(unreadCount);
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
    }
    
    // Update notification badge
    function updateNotificationBadge(count) {
        const bell = document.getElementById('notificationBell');
        
        if (count > 0) {
            notificationBadge.textContent = count > 99 ? '99+' : count;
            notificationBadge.style.display = 'block';
            notificationCount.textContent = `(${count})`;
            
            // Add visual indicator to bell
            if (bell) {
                bell.classList.add('has-notifications');
                bell.setAttribute('title', `${count} notifikasi baru`);
            }
        } else {
            notificationBadge.style.display = 'none';
            notificationCount.textContent = '(0)';
            
            // Remove visual indicator from bell
            if (bell) {
                bell.classList.remove('has-notifications');
                bell.setAttribute('title', 'Tidak ada notifikasi baru');
            }
        }
    }
    
    // Update notification dropdown content
    function updateNotificationDropdown(notifications) {
        if (notifications.length === 0) {
            notificationItems.innerHTML = '<div class="text-center py-3 text-muted">Tidak ada notifikasi</div>';
            return;
        }
        
        const notificationsHtml = notifications.map(notification => {
            const unreadClass = notification.is_read ? '' : 'unread';
            const iconColor = notification.color || 'warning';
            
            return `
                <a href="#" class="notification-item ${unreadClass}" onclick="markAsRead(${notification.id}, '${notification.data?.action_url || '#'}')">
                    <i class="${notification.icon} notification-item-icon text-${iconColor}"></i>
                    <div class="notification-item-content">
                        <div class="notification-item-title">${notification.title}</div>
                        <div class="notification-item-message">${notification.message}</div>
                        <div class="notification-item-time">${notification.time_ago}</div>
                    </div>
                </a>
            `;
        }).join('');
        
        notificationItems.innerHTML = notificationsHtml;
    }
    
    // Mark single notification as read
    window.markAsRead = function(notificationId, actionUrl) {
        fetch(`/admin/notifications/${notificationId}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications(); // Refresh notifications
                
                // Close dropdown and navigate if there's an action URL
                const notificationMenu = document.getElementById('notificationMenu');
                if (notificationMenu) {
                    notificationMenu.classList.remove('show');
                }
                
                if (actionUrl && actionUrl !== '#') {
                    setTimeout(() => {
                        window.location.href = actionUrl;
                    }, 200);
                }
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    };
    
    // Mark all notifications as read
    markAllRead.addEventListener('click', function(e) {
        e.preventDefault();
        
        fetch('/admin/notifications/mark-all-read', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications(); // Refresh notifications
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    });
});
</script>
@endif
