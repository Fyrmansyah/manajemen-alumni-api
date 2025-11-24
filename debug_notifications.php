<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

echo "=== DEBUGGING NOTIFICATIONS ===\n";

// Check total notifications
$totalNotifications = App\Models\Notification::count();
echo "Total notifications in database: $totalNotifications\n\n";

// Check recent notifications
echo "Recent notifications:\n";
$recent = App\Models\Notification::orderBy('created_at', 'desc')->take(5)->get(['id', 'title', 'user_id', 'created_at', 'type']);
foreach($recent as $n) {
    echo "ID: {$n->id} | Title: {$n->title} | User ID: {$n->user_id} | Type: {$n->type} | Created: {$n->created_at}\n";
}

echo "\n";

// Check admins
echo "Admins in database:\n";
$admins = App\Models\Admin::all(['id', 'username']);
foreach($admins as $admin) {
    echo "Admin ID: {$admin->id} | Username: {$admin->username}\n";
}

echo "\n";

// Check recent companies
echo "Recent companies:\n";
$companies = App\Models\Company::orderBy('created_at', 'desc')->take(3)->get(['id', 'company_name', 'created_at']);
foreach($companies as $company) {
    echo "Company ID: {$company->id} | Name: {$company->company_name} | Created: {$company->created_at}\n";
}

echo "\n";

// Test notification creation
echo "Testing notification creation for latest company...\n";
$latestCompany = App\Models\Company::latest()->first();
if ($latestCompany) {
    echo "Latest company: {$latestCompany->company_name}\n";
    try {
        $notifications = App\Models\Notification::createCompanyRegistration($latestCompany);
        echo "Created " . count($notifications) . " notifications\n";
    } catch (Exception $e) {
        echo "Error creating notifications: " . $e->getMessage() . "\n";
    }
} else {
    echo "No companies found\n";
}