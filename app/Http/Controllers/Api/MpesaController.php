<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MpesaService;

class MpesaController extends Controller
{
    public function __construct(
        protected MpesaService $mpesaService
    ) {}

    public function token()
    {
        $token = $this->mpesaService
            ->getAccessToken();

        return response()->json([
            'token' => $token,
        ]);
    }
}