<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
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
    $tasks = auth()->user()->tasks;
    return view('dashboard', compact('tasks'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Logout Route (only for logged-in users)
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('home');
})->name('logout')->middleware('auth');

// task routes
Route::resource('tasks', TaskController::class)->middleware('auth');
Route::prefix('tasks')->middleware('auth')->group(function () {
    Route::delete('{task}/remove-attachment', [TaskController::class, 'removeAttachment'])->name('tasks.removeAttachment');
});

