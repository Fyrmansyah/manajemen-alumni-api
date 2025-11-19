<?php

// Create notification for latest company manually
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CREATING MANUAL NOTIFICATION FOR LATEST COMPANY ===\n\n";

try {
    // Get latest company
    $latestCompany = App\Models\Company::latest()->first();
    
    if ($latestCompany) {
        echo "Latest company: {$latestCompany->company_name}\n";
        echo "Created at: {$latestCompany->created_at}\n\n";
        
        // Create notification manually
        $notifications = App\Models\Notification::createCompanyRegistration($latestCompany);
        
        echo "✅ Created " . count($notifications) . " notifications\n\n";
        
        // List all recent notifications
        echo "Recent notifications after creation:\n";
        $recent = App\Models\Notification::orderBy('created_at', 'desc')->take(3)->get();
        
        foreach($recent as $n) {
            $readStatus = $n->is_read ? 'Read' : 'UNREAD';
            echo "- ID: {$n->id} | Title: {$n->title} | Status: $readStatus | Created: {$n->created_at}\n";
        }
        
    } else {
        echo "No companies found\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}