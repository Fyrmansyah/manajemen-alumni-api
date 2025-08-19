<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\Validator;

$data = ['title' => 'Test','content' => 'X','category' => 'job','status' => 'published'];
$rules = [
    'title' => 'required|string|max:255',
    'excerpt' => 'nullable|string|max:500',
    'content' => 'required|string',
    'category' => 'required|in:info,achievement,job,event,announcement',
    'status' => 'required|in:draft,published,archived',
];

$v = Validator::make($data, $rules);
if ($v->fails()) {
    echo "FAIL:\n" . json_encode($v->errors()->all()) . "\n";
} else {
    echo "OK\n";
}
