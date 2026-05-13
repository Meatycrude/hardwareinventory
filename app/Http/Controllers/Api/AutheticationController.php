<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;

class AutheticationController extends Controller
{
    public function store(LoginRequest $loginRequest)
    {
        $credentials = $loginRequest->validated();

        $authResponse = Auth::attempt($credentials);

        if ($authResponse) {

            /**
             * @var User $user
             */
            $user = Auth::user();

            $accessToken = $user->createToken(config('auth.token_name'), ['*'], now()->addWeek());

            return response()->json([
                'access_token' => $accessToken->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $accessToken->accessToken->expires_at?->toIso8601String(CarbonInterface::DIFF_ABSOLUTE),
                'user' => $user
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
