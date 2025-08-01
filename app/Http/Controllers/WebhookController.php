<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Log semua data webhook dari Fonnte
        \Log::info('Fonnte Webhook', $request->all());

        // Tambahkan logika lain jika perlu, misal update status pesan

        return response()->json(['status' => 'ok']);
    }
}
