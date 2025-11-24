<?php

// Test creating new notifications with the new system
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING NEW NOTIFICATION SYSTEM ===\n\n";

try {
    // Get latest company
    $latestCompany = App\Models\Company::latest()->first();
    
    if (!$latestCompany) {
        echo "No companies found. Creating a test company...\n";
        $latestCompany = App\Models\Company::create([
            'company_name' => 'Test Company ' . date('Y-m-d H:i:s'),
            'email' => 'test' . time() . '@example.com',
            'phone' => '08123456789',
            'address' => 'Test Address',
            'contact_person' => 'Test Person',
            'contact_person_phone' => '08123456789',
            'password' => bcrypt('password123'),
            'status' => 'pending'
        ]);
        echo "✓ Test company created: {$latestCompany->company_name}\n\n";
    }

    echo "Using company: {$latestCompany->company_name}\n";
    echo "Company ID: {$latestCompany->id}\n\n";

    // Test the new notification creation
    echo "Creating notifications using new system...\n";
    $notifications = App\Models\Notification::createCompanyRegistration($latestCompany);
    
    echo "✓ Created " . count($notifications) . " notifications\n\n";

    // Check the notifications
    echo "Checking created notifications:\n";
    foreach($notifications as $notification) {
        echo "- ID: {$notification->id} | Title: {$notification->title} | User ID: {$notification->user_id} | Read: " . ($notification->is_read ? 'Yes' : 'No') . "\n";
    }
    echo "\n";

    // Test the query that the controller uses
    echo "Testing controller query for admin ID 1...\n";
    $adminId = 1;
    $controllerNotifications = App\Models\Notification::where(function ($query) use ($adminId) {
                                $query->whereNull('user_id')
                                      ->orWhere('user_id', $adminId);
                            })
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get(['id', 'title', 'user_id', 'is_read', 'created_at']);

    echo "Found " . count($controllerNotifications) . " notifications:\n";
    $unreadCount = 0;
    foreach($controllerNotifications as $n) {
        $readStatus = $n->is_read ? 'Read' : 'UNREAD';
        if (!$n->is_read) $unreadCount++;
        echo "- ID: {$n->id} | Title: {$n->title} | User: {$n->user_id} | Status: $readStatus | Created: {$n->created_at}\n";
    }
    
    echo "\n📊 Summary:\n";
    echo "Total notifications: " . count($controllerNotifications) . "\n";
    echo "Unread notifications: $unreadCount\n";
    
    if ($unreadCount > 0) {
        echo "✅ SUCCESS: Unread notifications found! Badge should show: $unreadCount\n";
    } else {
        echo "❌ ISSUE: No unread notifications found. Badge will not show.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}