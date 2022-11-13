<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        //
    }

    public function register(Request $request)
    {

        $data = $request->validate([
            'name'                      => ['required', 'max:255'],
            'email'                     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'                  => ['required', 'string', 'min:1', 'max:255', 'confirmed'],
            'password_confirmation'     => ['required', 'string', 'min:1', 'max:255'],
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $accessToken = $user->createToken('authToken')->accessToken;

        return [
            'user'          => $user,
            'accessToken'   => $accessToken,
        ];

    }

    public function login(Request $request)
    {

        $data = $request->validate([
            'email'      => ['required', 'email', 'max:255'],
            'password'   => ['required', 'max:255'],
        ]);

        if(!auth()->attempt($data))
        {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return [
            'user'          => auth()->user(),
            'accessToken'   => $accessToken,
        ];

    }

}
