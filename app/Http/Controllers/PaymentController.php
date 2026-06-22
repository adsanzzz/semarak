<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function createQRIS(Request $request)
{
    $response = Http::withBasicAuth(env('XENDIT_SECRET_KEY'), '')
        ->post('https://api.xendit.co/qr_codes', [
            'external_id' => 'order-' . time(),
            'type' => 'DYNAMIC',
            'amount' => 10000, // Ganti dengan jumlah yang sesuai
              'callback_url' => 'https://your-domain.com/webhook/xendit'
        ]);

    return $response->json();
}
}

