<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //Regisztáció

    public function register(Request $request)
    {
        $request -> validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Sikeres regisztráció', 'user' => $user], 201);
    }

    //Bejelentkezés: personal_access_tokens táblába felvesz egy rekordot

    public function login(Request $request)
    {
        $request -> validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response() ->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    //Kijelentkezés: personal_access_tokens táblából törli a rekordot
    
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(["message"=>"Sikeres kijelentkezés"], 200);
    }
}
