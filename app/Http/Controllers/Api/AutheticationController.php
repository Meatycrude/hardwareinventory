<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AutheticationController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        $code = random_int(100000, 999999);

        $user->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new TwoFactorCodeMail($code));

        return response()->json([
            'requires_2fa' => true,
            'email' => $user->email,
            'message' => 'Verification code sent.',
        ]);
    }

    public function verifyTwoFactor(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $user = User::where(
            'email',
            $validated['email']
        )->first();

        $submittedCode = trim(
            (string) $validated['code']
        );

        $storedCode = trim(
            (string) $user->two_factor_code
        );

        logger('2FA DEBUG', [
            'email_from_request' => $validated['email'],
            'submitted_code' => $submittedCode,
            'stored_code' => $storedCode,
            'expires_at' => $user?->two_factor_expires_at,
            'is_expired' => $user?->two_factor_expires_at?->isPast(),
        ]);

        if (
            ! $user ||
            ! $user->two_factor_code ||
            ! $user->two_factor_expires_at ||
            $storedCode !== $submittedCode ||
            $user->two_factor_expires_at->isPast()
        ) {
            return response()->json([
                'message' => 'Invalid or expired verification code.',
            ], 422);
        }

        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);

        $accessToken = $user->createToken(
            config('auth.token_name'),
            ['*'],
            now()->addWeek()
        );

        return response()->json([
            'access_token' => $accessToken->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function destroy(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->currentAccessToken()?->delete();

        return response()->noContent();
    }
}
