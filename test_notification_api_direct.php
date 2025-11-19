<?php

// Test the API endpoints directly to debug notification loading
echo "=== Testing Notification API Endpoints ===\n\n";

// Test 1: Check database directly
echo "1. Database Check:\n";
$pdo = new PDO('mysql:host=localhost;dbname=manajemen_alumni', 'root', '');
$stmt = $pdo->prepare("SELECT id, title, is_read, user_id, created_at FROM notifications WHERE user_id = 1 ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($notifications as $n) {
    $status = $n['is_read'] ? 'READ' : 'UNREAD';
    echo "- ID: {$n['id']} | Title: {$n['title']} | Status: $status | Created: {$n['created_at']}\n";
}

echo "\n2. API Simulation:\n";

// Simulate the exact query from AdminNotificationController
$query = "
    SELECT * FROM notifications 
    WHERE (user_id IS NULL OR user_id = 1) 
    ORDER BY created_at DESC 
    LIMIT 15
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$apiResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "API would return " . count($apiResults) . " notifications:\n";
foreach($apiResults as $n) {
    $status = $n['is_read'] ? 'READ' : 'UNREAD';
    echo "- ID: {$n['id']} | Title: {$n['title']} | Status: $status | User: {$n['user_id']}\n";
}

// Count unread
$unreadCount = 0;
foreach($apiResults as $n) {
    if (!$n['is_read']) $unreadCount++;
}

echo "\nUnread count: $unreadCount\n";

if ($unreadCount > 0) {
    echo "✅ SUCCESS: Should show badge with count $unreadCount\n";
} else {
    echo "❌ ISSUE: No unread notifications - badge won't show\n";
}