<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a fresh unread notification
DB::table('notifications')->insert([
    'type' => 'company_registered',
    'title' => 'Perusahaan Baru Terdaftar',
    'message' => 'Perusahaan Testing telah mendaftar dan menunggu verifikasi.',
    'data' => json_encode([
        'company_id' => 1,
        'company_name' => 'Testing',
        'action_url' => '#'
    ]),
    'icon' => 'fas fa-building',
    'color' => 'warning',
    'user_id' => 1,
    'is_read' => false,
    'created_at' => now(),
    'updated_at' => now()
]);

echo "✅ Created fresh unread notification\n";

// Verify it exists
$unread = DB::table('notifications')->where('is_read', false)->count();
echo "Total unread notifications: $unread\n";