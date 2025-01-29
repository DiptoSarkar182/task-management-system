<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Show Login Form
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// Handle Login Submission
Route::post('/login', [AuthController::class, 'login']);

// Show Registration Form
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

// Handle Registration & Auto Login
Route::post('/register', [AuthController::class, 'register']);

// Dashboard Route (only accessible when logged in)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Logout Route (only for logged-in users)
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('home');
})->name('logout')->middleware('auth');
