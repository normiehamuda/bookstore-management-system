<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(title="Bookstore Endpoints", version="0.1")
 * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 */
class AuthController extends Controller
{
  /**
   * @OA\Post(
   *     path="/api/login",
   *     summary="User login",
   *     tags={"Authentication"},
   *     @OA\RequestBody(
   *         required=true,
   *         description="User credentials",
   *         @OA\JsonContent(
   *             required={"email", "password"},
   *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
   *             @OA\Property(property="password", type="string", format="password", example="password")
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Successful login",
   *         @OA\JsonContent(
   *             @OA\Property(property="token", type="string", description="Sanctum API token"),
   *             @OA\Property(property="message", type="string", description="Success message")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Invalid credentials"
   *     )
   * )
   */
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

  /**
   * @OA\Post(
   *     path="/api/register",
   *     summary="User registration",
   *     tags={"Authentication"},
   *     @OA\RequestBody(
   *         required=true,
   *         description="User registration data",
   *         @OA\JsonContent(
   *             required={"name", "email", "password", "role"}, 
   *             @OA\Property(property="name", type="string", example="John Doe"),
   *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
   *             @OA\Property(property="password", type="string", format="password", example="password"),
   *             @OA\Property(property="role", type="integer", description="Role ID (1: Admin, 2: User)", example=2) 
   *         )
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Successful registration",
   *         @OA\JsonContent(
   *             @OA\Property(property="token", type="string", description="Sanctum API token"),
   *             @OA\Property(property="message", type="string", description="Success message")
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error"
   *     )
   * )
   */
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

  /**
   * @OA\Get(
   *     path="/api/logout",
   *     summary="User logout",
   *     tags={"Authentication"},
   *     security={{"bearer_token":{}}},
   *     @OA\Response(
   *         response=200,
   *         description="Successful logout"
   *     )
   * )
   */
  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
      'message' => 'User logged out successfully'
    ], 200);
  }
}