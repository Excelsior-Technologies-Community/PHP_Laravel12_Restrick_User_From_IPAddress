<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RSSFeedController;

/**
 * Routes protected by Block IP Middleware
 */
Route::middleware(['blockIP'])->group(function () {

    // User resource routes
    Route::resource('users', UserController::class);

    // RSS resource routes
    Route::resource('rss', RSSFeedController::class);
});
