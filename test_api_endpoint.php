<?php

// Test the actual API endpoint
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->instance('request', Illuminate\Http\Request::createFromGlobals());

echo "=== TESTING API ENDPOINT ===\n\n";

try {
    // Simulate admin login
    $admin = App\Models\Admin::first();
    Auth::guard('admin')->login($admin);
    
    echo "✓ Logged in as admin: {$admin->username}\n\n";

    // Test the getRecent method directly
    $controller = new App\Http\Controllers\Admin\AdminNotificationController();
    $request = Illuminate\Http\Request::create('/admin/notifications/recent', 'GET', ['limit' => 10]);
    
    $response = $controller->getRecent($request);
    $responseData = json_decode($response->getContent(), true);
    
    echo "API Response:\n";
    echo "Success: " . ($responseData['success'] ? 'Yes' : 'No') . "\n";
    echo "Data count: " . count($responseData['data']) . "\n\n";
    
    echo "Notifications in response:\n";
    foreach($responseData['data'] as $notification) {
        $readStatus = $notification['is_read'] ? 'Read' : 'UNREAD';
        echo "- ID: {$notification['id']} | Title: {$notification['title']} | Status: $readStatus | Time: {$notification['time_ago']}\n";
    }
    
    // Count unread
    $unreadCount = 0;
    foreach($responseData['data'] as $notification) {
        if (!$notification['is_read']) $unreadCount++;
    }
    
    echo "\n📊 API Summary:\n";
    echo "Unread in API response: $unreadCount\n";
    
    if ($unreadCount > 0) {
        echo "✅ SUCCESS: API should show $unreadCount unread notifications\n";
    } else {
        echo "❌ ISSUE: API shows no unread notifications\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}