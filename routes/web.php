<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IpRestrictionController;


/*
|--------------------------------------------------------------------------
| IP Restriction Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/ip-restrictions', [
    IpRestrictionController::class,
    'index'
])->name('ip-restrictions.index');


/*
|--------------------------------------------------------------------------
| Security Analytics
|--------------------------------------------------------------------------
*/

Route::get('/security-analytics', [
    IpRestrictionController::class,
    'analytics'
])->name('security-analytics.index');


/*
|--------------------------------------------------------------------------
| Store IP Restriction
|--------------------------------------------------------------------------
*/

Route::post('/ip-restrictions', [
    IpRestrictionController::class,
    'store'
])->name('ip-restrictions.store');


/*
|--------------------------------------------------------------------------
| Export Restrictions
|--------------------------------------------------------------------------
*/

Route::get('/ip-restrictions/export', [
    IpRestrictionController::class,
    'exportRestrictions'
])->name('ip-restrictions.export');


/*
|--------------------------------------------------------------------------
| Export Logs
|--------------------------------------------------------------------------
*/

Route::get('/ip-restrictions/logs/export', [
    IpRestrictionController::class,
    'exportLogs'
])->name('ip-restrictions.logs.export');


/*
|--------------------------------------------------------------------------
| Clear Logs
|--------------------------------------------------------------------------
*/

Route::delete('/ip-restrictions/logs/clear', [
    IpRestrictionController::class,
    'clearLogs'
])->name('ip-restrictions.logs.clear');


/*
|--------------------------------------------------------------------------
| Activate
|--------------------------------------------------------------------------
*/

Route::patch(
    '/ip-restrictions/{ipRestriction}/activate',
    [
        IpRestrictionController::class,
        'activate'
    ]
)->name('ip-restrictions.activate');


/*
|--------------------------------------------------------------------------
| Deactivate
|--------------------------------------------------------------------------
*/

Route::patch(
    '/ip-restrictions/{ipRestriction}/deactivate',
    [
        IpRestrictionController::class,
        'deactivate'
    ]
)->name('ip-restrictions.deactivate');


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

Route::delete(
    '/ip-restrictions/{ipRestriction}',
    [
        IpRestrictionController::class,
        'destroy'
    ]
)->name('ip-restrictions.destroy');


/*
|--------------------------------------------------------------------------
| Protected Test Route
|--------------------------------------------------------------------------
*/

Route::middleware(['blockIP'])->get(
    '/protected',
    function () {

        return response()->json([
            'success' => true,

            'message' =>
            'Your IP address is allowed to access this protected page.',

            'ip_address' => request()->ip(),
        ]);
    }
);
