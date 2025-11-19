<?php

// Test admin notifications API endpoints
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->instance('request', Illuminate\Http\Request::createFromGlobals());

echo "=== TESTING ADMIN NOTIFICATIONS API ===\n\n";

try {
    // Simulate admin login
    $admin = App\Models\Admin::first();
    Auth::guard('admin')->login($admin);
    
    echo "✓ Logged in as admin: {$admin->username}\n\n";

    // Test the index method (AJAX request)
    $controller = new App\Http\Controllers\Admin\AdminNotificationController();
    
    // Create AJAX request
    $request = Illuminate\Http\Request::create('/admin/notifications', 'GET', ['per_page' => 15]);
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    $request->headers->set('Accept', 'application/json');
    
    echo "1. Testing index method (AJAX)...\n";
    $response = $controller->index($request);
    $responseData = json_decode($response->getContent(), true);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Success: " . ($responseData['success'] ? 'Yes' : 'No') . "\n";
    
    if ($responseData['success']) {
        echo "Data Structure: " . (isset($responseData['data']['data']) ? 'Paginated' : 'Simple Array') . "\n";
        $notifications = $responseData['data']['data'] ?? $responseData['data'];
        echo "Notifications Count: " . count($notifications) . "\n";
        
        echo "\nFirst notification:\n";
        if (!empty($notifications)) {
            $first = $notifications[0];
            echo "- ID: {$first['id']}\n";
            echo "- Title: {$first['title']}\n";
            echo "- Read: " . ($first['is_read'] ? 'Yes' : 'No') . "\n";
        }
    } else {
        echo "Error: " . ($responseData['message'] ?? 'Unknown error') . "\n";
    }
    
    echo "\n2. Testing markAsRead method...\n";
    $notificationId = 4; // Use existing notification ID
    $response = $controller->markAsRead($notificationId);
    $responseData = json_decode($response->getContent(), true);
    
    echo "Mark as Read Status: " . $response->getStatusCode() . "\n";
    echo "Mark as Read Success: " . ($responseData['success'] ? 'Yes' : 'No') . "\n";
    
    echo "\n3. Testing delete method...\n";
    // Don't actually delete, just test if the notification exists
    $notification = App\Models\Notification::where('id', $notificationId)
        ->where(function ($query) use ($admin) {
            $query->whereNull('user_id')
                  ->orWhere('user_id', $admin->id);
        })
        ->first();
    
    echo "Notification found for delete: " . ($notification ? 'Yes' : 'No') . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}