<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $credentials = $request->validate(
      [
        'email' => 'required|email',
        'password' => 'required'
      ]
    );

    if (Auth::attempt($credentials)) {
      $user = Auth::user();
      $user =  User::find($user->id);
      return response()->json([
        'token' => $user->createToken('auth-token')->plainTextToken,
        'message' => 'User logged in successfully'
      ]);
    }

    return response()->json([
      'message' => 'Invalid credentials'
    ], 401);
  }


  public function register(Request $request)
  {
    $request->validate(
      [
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'role' => 'required|exists:roles,id'
      ]
    );

    $user = User::create([
      'name' => $request->input('name'),
      'email' => $request->input('email'),
      'password' => Hash::make($request->input('password')),
      'role_id' => $request->get('role')
    ]);

    return  response()->json([
      'token' => $user->createToken('auth-token')->plainTextToken,
      'message' => 'User registered successfully'
    ], 201);
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
      'message' => 'User logged out successfully'
    ], 200);
  }
}