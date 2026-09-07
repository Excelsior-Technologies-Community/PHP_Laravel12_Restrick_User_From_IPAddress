<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IpRestrictionController;

/*
|--------------------------------------------------------------------------
| IP Restriction Management
|--------------------------------------------------------------------------
|
| These routes allow you to manage blocked IP addresses,
| activate/deactivate restrictions, and view blocked attempts.
|
*/

Route::get('/ip-restrictions', [
    IpRestrictionController::class,
    'index'
])->name('ip-restrictions.index');

Route::get('/security-analytics', [
    IpRestrictionController::class,
    'analytics'
])->name('security-analytics.index');

Route::post('/ip-restrictions', [
    IpRestrictionController::class,
    'store'
])->name('ip-restrictions.store');

/*
|--------------------------------------------------------------------------
| Clear blocked IP logs
|--------------------------------------------------------------------------
*/

Route::delete('/ip-restrictions/logs/clear', [
    IpRestrictionController::class,
    'clearLogs'
])->name('ip-restrictions.logs.clear');

/*
|--------------------------------------------------------------------------
| Activate / Deactivate IP restriction
|--------------------------------------------------------------------------
*/

Route::patch('/ip-restrictions/{ipRestriction}/activate', [
    IpRestrictionController::class,
    'activate'
])->name('ip-restrictions.activate');

Route::patch('/ip-restrictions/{ipRestriction}/deactivate', [
    IpRestrictionController::class,
    'deactivate'
])->name('ip-restrictions.deactivate');

/*
|--------------------------------------------------------------------------
| Delete IP restriction
|--------------------------------------------------------------------------
*/

Route::delete('/ip-restrictions/{ipRestriction}', [
    IpRestrictionController::class,
    'destroy'
])->name('ip-restrictions.destroy');


/*
|--------------------------------------------------------------------------
| Test Route Protected By IP Middleware
|--------------------------------------------------------------------------
|
| Use this route to test whether an IP address is blocked.
|
*/

Route::middleware(['blockIP'])->get('/protected', function () {
    return response()->json([
        'success' => true,
        'message' => 'Your IP address is allowed to access this protected page.',
        'ip_address' => request()->ip(),
    ]);
});