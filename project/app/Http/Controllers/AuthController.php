<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $rules = array
        (
            'name' => ['required', 'string', 'min:1', 'max:50'],
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required'],
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response()->json(['message' => 'Validation error'], 422);
        }

        $validated = $validator->validated();

        $user = User::create
        ([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;
        $user->forceFill(['remember_token' => $token])->save();

        return response()->json(['message' => 'Registration success', 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $rules = array
        (
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        );
        
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response()->json(['message' => 'Validation error'], 422);
        }

        $validated = $validator->validated();

        $user = User::query()->where('email', '=', $validated['email'])->where('password', '=', $validated['password'])->first();

        if($user)
        {
            $token = $user->createToken('myapptoken')->plainTextToken;

            $user->forceFill(['remember_token' => $token,])->save();

            return response()->json(['message' => 'Auth success', 'token' => $token], 200);
        }
        
        else
        {
            return response()->json(['message' => 'Auth failed'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $request->user()->forceFill(['remember_token' => '',])->save();

        return response()->json(['message' => 'logout'], 200);
    }
}
