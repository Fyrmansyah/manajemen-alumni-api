<?php

// Sync notifications for companies that don't have notifications yet
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SYNCING COMPANY NOTIFICATIONS ===\n\n";

try {
    // Get all companies
    $companies = App\Models\Company::orderBy('created_at', 'desc')->get();
    echo "Total companies: " . $companies->count() . "\n\n";
    
    // Get existing notifications for companies
    $existingNotifications = App\Models\Notification::where('type', 'company_registered')
        ->get()
        ->keyBy(function($item) {
            $data = is_string($item->data) ? json_decode($item->data, true) : $item->data;
            return $data['company_id'] ?? null;
        });
    
    echo "Existing company notifications: " . $existingNotifications->count() . "\n\n";
    
    $created = 0;
    foreach ($companies as $company) {
        if (!$existingNotifications->has($company->id)) {
            echo "Creating notification for: {$company->company_name} (ID: {$company->id})\n";
            
            $notifications = App\Models\Notification::createCompanyRegistration($company);
            $created += count($notifications);
        } else {
            echo "Notification already exists for: {$company->company_name}\n";
        }
    }
    
    echo "\n✅ Created $created new notifications\n\n";
    
    // Show recent notifications
    echo "Recent notifications (unread only):\n";
    $recentUnread = App\Models\Notification::where('is_read', false)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    
    foreach($recentUnread as $n) {
        echo "- ID: {$n->id} | Title: {$n->title} | Created: {$n->created_at}\n";
    }
    
    if ($recentUnread->isEmpty()) {
        echo "No unread notifications found\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
}