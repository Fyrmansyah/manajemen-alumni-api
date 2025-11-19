<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    /**
     * Get unread notifications count for admin
     */
    public function getUnreadCount()
    {
        try {
            if (!Auth::guard('admin')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $adminId = Auth::guard('admin')->id();
            $count = Notification::unread()
                        ->where(function ($query) use ($adminId) {
                            $query->whereNull('user_id')
                                  ->orWhere('user_id', $adminId);
                        })
                        ->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getUnreadCount: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Get recent notifications for admin
     */
    public function getRecent(Request $request)
    {
        try {
            if (!Auth::guard('admin')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $adminId = Auth::guard('admin')->id();
            $limit = $request->get('limit', 10);

            $notifications = Notification::where(function ($query) use ($adminId) {
                                $query->whereNull('user_id')
                                      ->orWhere('user_id', $adminId);
                            })
                            ->orderBy('created_at', 'desc')
                            ->limit($limit)
                            ->get();

            return response()->json([
                'success' => true,
                'data' => $notifications->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'icon' => $notification->icon ?? 'fas fa-bell',
                        'color' => $notification->color ?? 'primary',
                        'is_read' => $notification->is_read,
                        'time_ago' => $notification->time_ago,
                        'data' => $notification->data
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getRecent: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $adminId = Auth::guard('admin')->id();
        
        $notification = Notification::where('id', $id)
                        ->where(function ($query) use ($adminId) {
                            $query->whereNull('user_id')
                                  ->orWhere('user_id', $adminId);
                        })
                        ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $adminId = Auth::guard('admin')->id();
        
        Notification::where(function ($query) use ($adminId) {
                        $query->whereNull('user_id')
                              ->orWhere('user_id', $adminId);
                    })
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now()
                    ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Get all notifications with pagination
     */
    public function index(Request $request)
    {
        try {
            // Check if user is authenticated
            if (!Auth::guard('admin')->check()) {
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized'
                    ], 401);
                }
                return redirect()->route('login');
            }

            // If this is an AJAX request, return JSON
            if ($request->ajax() || $request->expectsJson()) {
                $adminId = Auth::guard('admin')->id();
                $perPage = $request->get('per_page', 15);

                \Log::info('Loading notifications for admin: ' . $adminId);

                $notifications = Notification::where(function ($query) use ($adminId) {
                                    $query->whereNull('user_id')
                                          ->orWhere('user_id', $adminId);
                                })
                                ->orderBy('created_at', 'desc')
                                ->paginate($perPage);

                \Log::info('Found notifications: ' . $notifications->count());

                return response()->json([
                    'success' => true,
                    'data' => $notifications
                ]);
            }

            // For web view, return the Blade template
            return view('admin.notifications.index');
            
        } catch (\Exception $e) {
            \Log::error('Error in AdminNotificationController@index: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Server error: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $adminId = Auth::guard('admin')->id();
        
        $notification = Notification::where('id', $id)
                        ->where(function ($query) use ($adminId) {
                            $query->whereNull('user_id')
                                  ->orWhere('user_id', $adminId);
                        })
                        ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }
}
