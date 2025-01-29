<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Mock Login Page
Route::get('/login', function () {
    return "Login Page (Implement Authentication)";
})->name('login');

// Mock Register Page
Route::get('/register', function () {
    return "Register Page (Implement Authentication)";
})->name('register');

// Dashboard Route (requires authentication)
Route::get('/dashboard', function () {
    return "This is the dashboard";
})->middleware('auth');

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

