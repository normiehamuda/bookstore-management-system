<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
  Route::match(['post'], '/login', [AuthController::class, 'login'])->name('login');
  Route::match(['post'], '/register', [AuthController::class, 'register'])->name('register');
});

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/user', function (Request $request) {
    return $request->user();
  });
  Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/books/search', [BookController::class, 'search']);
Route::get('/books/elastic-search', [BookController::class, 'elasticSearch']);

Route::resource('books', BookController::class)->only([
  'index',
  'show'
])->middleware('guest');

Route::resource('books', BookController::class)->only([
  'store',
  'update',
  'destroy'
])->middleware(['auth:sanctum', EnsureUserIsAdmin::class]);