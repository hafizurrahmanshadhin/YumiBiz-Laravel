<?php

use App\Http\Controllers\Auth\SocialLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login/google', [SocialLoginController::class, 'googleRedirect'])->name('login.google');
Route::get('/login/google/callback', [SocialLoginController::class, 'googleCallback']);
