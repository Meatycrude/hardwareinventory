<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MpesaService
{
    public function getAccessToken()
    {
        $consumerKey = config('services.mpesa.consumer_key');

        $consumerSecret = config('services.mpesa.consumer_secret');

        $response = Http::withBasicAuth(
            $consumerKey,
            $consumerSecret
        )->get(
            'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
        );

        return $response->json()['access_token'];
    }
}
