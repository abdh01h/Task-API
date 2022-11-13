<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

    public function __construct()
    {
        //
    }

    public function updateProfile(Request $request)
    {

        $user_id = auth()->user()->id;

        $data = $request->validate([
            'name'  => ['required', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user_id],
        ]);

        if(auth()->user()->update($data))
        {
            return ['message' => 'Profile updated successfully'];
        }

        return response()->json(['message' => 'Error occurred, please try again later!'], 500);

    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        if(!Hash::check($request->password, $user->password))
        {
            return response()->json(['message' => 'Your current password is incorrect!'], 401);
        }

        $data = $request->validate([
            'password'                      => ['required', 'string', 'min:1', 'max:255'],
            'new_password'                  => ['required', 'string', 'min:1', 'max:255', 'confirmed'],
            'new_password_confirmation'     => ['required', 'string', 'min:1', 'max:255'],

        ]);

        $user->password = Hash::make($data['new_password']);

        if($user->save())
        {
            return ['message' => 'Password updated successfully'];
        } else {
            return response()->json(['message' => 'Error occurred, please try again later!'], 500);
        }

    }

}
