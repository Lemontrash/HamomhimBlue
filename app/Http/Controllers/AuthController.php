<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function signup(Request $request)
    {
        $request->validate([
            'name'              => 'required|string',
            'email'             => 'required|string|email|unique:users',
            'password'          => 'required|string|confirmed',
            'city'              => 'required|string|max:191',
            'address'           => 'required|string|max:191',
            'phone'             => 'required|string|max:191',
            'business_phone'    => 'required|string|max:191',
            'working_area'      => 'required|string|max:191',
            'fax'               => 'required|string|max:191',
            'name_of_business'  => 'required|string|max:191',
        ]);
        $user = new User([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => bcrypt($request->password),
            'city'              => $request->city,
            'address'           => $request->address,
            'phone'             => $request->phone,
            'business_phone'    => $request->business_phone,
            'working_area'      => $request->working_area,
            'fax'               => $request->fax,
            'name_of_business'  => $request->name_of_business,
        ]);
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Successfully created user!'
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:191',
            'password' => 'required|string|max:191',
            'remember_me' => 'boolean',
        ]);
        $credentials = request(['email', 'password']);
        if(!\Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
