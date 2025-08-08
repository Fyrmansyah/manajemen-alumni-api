<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Tambahkan logika lain jika perlu, misal update status pesan

        return response()->json(['status' => 'ok']);
    }
}
