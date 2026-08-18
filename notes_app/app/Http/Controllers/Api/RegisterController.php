<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        // set validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email'  => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        // if validation fails
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create user
        $user = DB::selectOne('
            INSERT INTO users(name, email, password, created_at, updated_at)
            VALUES(?, ?, ?, NOW(), NOW())
            RETURNING id, name, email, created_at
        ', [
            $request->name,
            $request->email,
            Hash::make($request->password)
        ]);

        // return response JSON user is created
        if($user){
            return response()->json([
                'success' => true,
                'user' => $user
            ], 201);
        }

        // return JSON process insert failed
        return response()->json([
            'success' => false
        ], 409);
    }
}
