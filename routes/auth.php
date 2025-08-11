<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Simple login/logout routes for now
Route::get('login', function () {
    return view('auth.login');
})->name('login');

Route::post('login', function () {
    $credentials = request()->only('email', 'password');
    if (Auth::attempt($credentials)) {
        return redirect()->intended('/incidents');
    }
    return back()->withErrors(['email' => 'Invalid credentials']);
});

Route::post('logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');