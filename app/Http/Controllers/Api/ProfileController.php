<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json(
            $request->user()
        );
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([

            'current_password' => [
                'required',
            ],

            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],
        ]);

        $user = $request->user();

        if (! Hash::check(
            $validated['current_password'],
            $user->password
        )) {

            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update([
            'password' => bcrypt(
                $validated['password']
            ),
        ]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }
}
