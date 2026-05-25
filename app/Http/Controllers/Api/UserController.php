<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(
            User::latest()->get()
        );
    }

    public function store(Request $request)
    {
        if (
            $request->user()->role !== 'admin'
        ) {
            abort(403);
        }

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],

            'role' => [
                'required',
                'in:admin,cashier,storekeeper',
            ],
        ]);

        $user = User::create([

            'name' => $validated['name'],

            'email' => $validated['email'],

            'password' => bcrypt(
                $validated['password']
            ),

            'role' => $validated['role'],
        ]);

        return response()->json(
            $user,
            201
        );
    }
}