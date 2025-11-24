@extends('layouts.app')

@section('title', 'Kelola Notifikasi - BKK SMKN 1 Surabaya')

@section('content')
<div class="container-fluid mt-4 mb-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Kelola Notifikasi</h1>
                    <p class="text-muted mb-0">Daftar semua notifikasi sistem</p>
                </div>
                <div>
                    <button class="btn btn-outline-secondary" onclick="markAllAsRead()">
                        <i class="fas fa-check-double me-2"></i>Tandai Semua Dibaca
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-bell text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="card-title mb-1" id="totalNotifications">0</h5>
                            <p class="card-text text-muted mb-0">Total Notifikasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-envelope text-warning"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="card-title mb-1" id="unreadNotifications">0</h5>
                            <p class="card-text text-muted mb-0">Belum Dibaca</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-envelope-open text-success"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="card-title mb-1" id="readNotifications">0</h5>
                            <p class="card-text text-muted mb-0">Sudah Dibaca</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                <i class="fas fa-calendar-day text-info"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="card-title mb-1" id="todayNotifications">0</h5>
                            <p class="card-text text-muted mb-0">Hari Ini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Cari notifikasi..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter">
                                <option value="">Semua Tipe</option>
                                <option value="company_registered">Registrasi Perusahaan</option>
                                <option value="job_application">Lamaran Kerja</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="unread">Belum Dibaca</option>
                                <option value="read">Sudah Dibaca</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" onclick="applyFilters()">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Daftar Notifikasi</h5>
                </div>
                <div class="card-body p-0">
                    <div id="notificationsContainer">
                        <!-- Notifications will be loaded here -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Memuat notifikasi...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-12">
            <nav aria-label="Notification pagination">
                <ul class="pagination justify-content-center" id="paginationContainer">
                    <!-- Pagination will be loaded here -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Notifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus notifikasi ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Hapus</button>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.notification-item.unread {
    background-color: #fff3cd;
    border-left-color: #ffc107;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-actions {
    opacity: 0.7;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    flex-shrink: 0;
    margin-left: 16px;
    margin-top: -2px; /* Align with title */
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

.notification-actions .btn {
    padding: 4px 8px;
    font-size: 11px;
    line-height: 1;
    border-radius: 3px;
    min-width: auto;
}

.notification-actions .btn i {
    font-size: 10px;
}
</style>

<script>
let currentPage = 1;
let currentFilters = {};
let deleteNotificationId = null;

// Get CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Default fetch options with authentication
const fetchOptions = {
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    credentials: 'same-origin'
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Notifications page loaded');
    loadNotifications();
    loadStats();

    // Auto refresh every 30 seconds
    setInterval(() => {
        console.log('Auto-refreshing notifications');
        loadNotifications();
        loadStats();
    }, 30000);
});

function loadStats() {
    console.log('Loading stats...');
    
    // This would normally fetch from API, for now we'll calculate from current data
    fetch('/admin/notifications?per_page=1000', fetchOptions)
        .then(response => {
            console.log('Stats response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Stats response data:', data);
            if (data.success) {
                const notifications = data.data.data || data.data;
                const total = notifications.length;
                const unread = notifications.filter(n => !n.is_read).length;
                const read = total - unread;
                const today = notifications.filter(n => {
                    const created = new Date(n.created_at);
                    const now = new Date();
                    return created.toDateString() === now.toDateString();
                }).length;

                console.log('Stats calculated:', { total, unread, read, today });

                document.getElementById('totalNotifications').textContent = total;
                document.getElementById('unreadNotifications').textContent = unread;
                document.getElementById('readNotifications').textContent = read;
                document.getElementById('todayNotifications').textContent = today;
            } else {
                console.error('Stats API error:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
}

function loadNotifications(page = 1) {
    currentPage = page;
    
    let url = `/admin/notifications?page=${page}`;
    
    // Add filters
    const params = new URLSearchParams();
    if (currentFilters.search) params.append('search', currentFilters.search);
    if (currentFilters.type) params.append('type', currentFilters.type);
    if (currentFilters.status) params.append('status', currentFilters.status);
    
    if (params.toString()) {
        url += '&' + params.toString();
    }

    console.log('Loading notifications from:', url);

    fetch(url, fetchOptions)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                renderNotifications(data.data.data);
                renderPagination(data.data);
            } else {
                console.error('API returned error:', data.message);
                document.getElementById('notificationsContainer').innerHTML = 
                    `<div class="text-center py-5 text-danger">Error: ${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            document.getElementById('notificationsContainer').innerHTML = 
                `<div class="text-center py-5 text-danger">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <p>Error loading notifications: ${error.message}</p>
                    <button class="btn btn-primary" onclick="loadNotifications()">Retry</button>
                </div>`;
        });
}

function renderNotifications(notifications) {
    const container = document.getElementById('notificationsContainer');
    
    if (notifications.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="fas fa-bell-slash fa-3x mb-3"></i>
                <p>Tidak ada notifikasi</p>
            </div>
        `;
        return;
    }

    const notificationsHtml = notifications.map(notification => {
        const unreadClass = notification.is_read ? '' : 'unread';
        const iconColor = notification.color || 'warning';
        const data = typeof notification.data === 'string' ? JSON.parse(notification.data) : notification.data;
        
        return `
            <div class="notification-item ${unreadClass} border-bottom p-3">
                <div class="d-flex align-items-start">
                    <div class="notification-icon bg-${iconColor} bg-opacity-10 me-3">
                        <i class="${notification.icon} text-${iconColor}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${notification.title}</h6>
                                <p class="mb-1 text-muted small">${notification.message}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>${formatDate(notification.created_at)}
                                </small>
                            </div>
                            <div class="notification-actions">
                                ${!notification.is_read ? `
                                    <button class="btn btn-sm btn-outline-success me-2" onclick="markAsRead(${notification.id})" title="Tandai Dibaca">
                                        <i class="fas fa-check"></i>
                                    </button>
                                ` : ''}
                                <button class="btn btn-sm btn-outline-danger" onclick="showDeleteModal(${notification.id})" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    container.innerHTML = notificationsHtml;
}

function renderPagination(paginationData) {
    const container = document.getElementById('paginationContainer');
    
    if (paginationData.last_page <= 1) {
        container.innerHTML = '';
        return;
    }

    let paginationHtml = '';
    
    // Previous button
    if (paginationData.current_page > 1) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadNotifications(${paginationData.current_page - 1})">Previous</a>
            </li>
        `;
    }

    // Page numbers
    for (let i = 1; i <= paginationData.last_page; i++) {
        const activeClass = i === paginationData.current_page ? 'active' : '';
        paginationHtml += `
            <li class="page-item ${activeClass}">
                <a class="page-link" href="#" onclick="loadNotifications(${i})">${i}</a>
            </li>
        `;
    }

    // Next button
    if (paginationData.current_page < paginationData.last_page) {
        paginationHtml += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadNotifications(${paginationData.current_page + 1})">Next</a>
            </li>
        `;
    }

    container.innerHTML = paginationHtml;
}

function applyFilters() {
    currentFilters = {
        search: document.getElementById('searchInput').value,
        type: document.getElementById('typeFilter').value,
        status: document.getElementById('statusFilter').value
    };
    
    loadNotifications(1);
}

function markAsRead(notificationId) {
    console.log('Marking notification as read:', notificationId);
    
    fetch(`/admin/notifications/${notificationId}/read`, {
        method: 'PATCH',
        ...fetchOptions
    })
    .then(response => {
        console.log('Mark as read response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Mark as read response:', data);
        if (data.success) {
            loadNotifications(currentPage);
            loadStats();
            // Show success message
            showToast('Notifikasi ditandai sebagai sudah dibaca', 'success');
        } else {
            showToast('Gagal menandai notifikasi sebagai dibaca', 'error');
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
        showToast('Error: ' + error.message, 'error');
    });
}

function markAllAsRead() {
    if (confirm('Tandai semua notifikasi sebagai sudah dibaca?')) {
        fetch('/admin/notifications/mark-all-read', {
            method: 'PATCH',
            ...fetchOptions
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications(currentPage);
                loadStats();
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    }
}

function showDeleteModal(notificationId) {
    deleteNotificationId = notificationId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function confirmDelete() {
    if (deleteNotificationId) {
        fetch(`/admin/notifications/${deleteNotificationId}`, {
            method: 'DELETE',
            ...fetchOptions
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications(currentPage);
                loadStats();
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                modal.hide();
            }
        })
        .catch(error => console.error('Error deleting notification:', error));
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInHours = (now - date) / (1000 * 60 * 60);
    
    if (diffInHours < 1) {
        const diffInMinutes = (now - date) / (1000 * 60);
        return `${Math.floor(diffInMinutes)} menit yang lalu`;
    } else if (diffInHours < 24) {
        return `${Math.floor(diffInHours)} jam yang lalu`;
    } else {
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

// Search on enter
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

// Simple toast notification function
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to body
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}
</script>
@endsection