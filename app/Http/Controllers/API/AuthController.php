<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validateData = Validator::make($request->all(),
        [
            'name' =>'required|max:255',
            'email' => 'email|required|unique:users',
            'password' => 'required',
            'password_confirmation' => 'required'
        ]);

        if($validateData->fails())
        {
            return response(['message' => 'Validation error']);
        }

        $token = Str::random(80);

        $user = User::create([
            'name' =>$request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => $token
        ]);

        return response(['message' => 'User registered successfully', 'token' => $token]);
    }

    public function login(Request $request)
    {
        $data = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required',
        ]);


        if($data->fails())
        {
            return response(['message' => 'Validation error']);
        }


        if(!Auth::attempt(['email' => $request->email,'password' => $request->password]))
        {
            return response(['message' => 'User does not exist']);
        }

        $user = User::where(['email' => $request->email])->first();

        return response(['message' => 'User authenticated', 'token' => $user->auth_token]);
    }
}
