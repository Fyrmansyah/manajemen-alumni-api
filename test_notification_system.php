<?php

// Test notification system step by step
require_once 'vendor/autoload.php';

// Bootstrap Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== NOTIFICATION SYSTEM TEST ===\n\n";

try {
    // 1. Check database connection
    echo "1. Testing database connection...\n";
    $pdo = DB::connection()->getPdo();
    echo "✓ Database connected\n\n";

    // 2. Check notifications table
    echo "2. Checking notifications table...\n";
    $notificationCount = DB::table('notifications')->count();
    echo "Total notifications: $notificationCount\n\n";

    // 3. Check recent notifications
    echo "3. Recent notifications:\n";
    $recent = DB::table('notifications')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['id', 'title', 'user_id', 'created_at', 'type', 'is_read']);
    
    foreach($recent as $n) {
        echo "ID: {$n->id} | Title: {$n->title} | User: {$n->user_id} | Type: {$n->type} | Read: " . ($n->is_read ? 'Yes' : 'No') . " | Created: {$n->created_at}\n";
    }
    echo "\n";

    // 4. Check admins
    echo "4. Checking admins...\n";
    $adminCount = DB::table('admins')->count();
    echo "Total admins: $adminCount\n";
    
    $admins = DB::table('admins')->get(['id', 'username']);
    foreach($admins as $admin) {
        echo "Admin ID: {$admin->id} | Username: {$admin->username}\n";
    }
    echo "\n";

    // 5. Test notification query for specific admin
    echo "5. Testing notification query for admin ID 1...\n";
    $adminId = 1;
    $adminNotifications = DB::table('notifications')
        ->where(function ($query) use ($adminId) {
            $query->whereNull('user_id')
                  ->orWhere('user_id', $adminId);
        })
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get(['id', 'title', 'user_id', 'is_read']);
    
    echo "Found " . count($adminNotifications) . " notifications for admin ID $adminId:\n";
    foreach($adminNotifications as $n) {
        echo "- ID: {$n->id} | Title: {$n->title} | User: {$n->user_id} | Read: " . ($n->is_read ? 'Yes' : 'No') . "\n";
    }
    echo "\n";

    // 6. Check companies
    echo "6. Recent companies:\n";
    $companies = DB::table('companies')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get(['id', 'company_name', 'created_at']);
    
    foreach($companies as $company) {
        echo "Company: {$company->company_name} | Created: {$company->created_at}\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}