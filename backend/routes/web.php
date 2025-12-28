<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect route for shortened links (must be last to avoid conflicts)
Route::get('/{slug}', [RedirectController::class, 'redirect'])
    ->where('slug', '[a-zA-Z0-9]{6,8}');
