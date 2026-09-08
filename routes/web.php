<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IpRestrictionController;

Route::get('/ip-restrictions', [IpRestrictionController::class, 'index'])
    ->name('ip-restrictions.index');

Route::get('/security-analytics', [IpRestrictionController::class, 'analytics'])
    ->name('security-analytics.index');

Route::post('/ip-restrictions', [IpRestrictionController::class, 'store'])
    ->name('ip-restrictions.store');

Route::post('/ip-restrictions/bulk-action', [IpRestrictionController::class, 'bulkAction'])
    ->name('ip-restrictions.bulk-action');

Route::post('/ip-restrictions/import', [IpRestrictionController::class, 'import'])
    ->name('ip-restrictions.import');

Route::get('/ip-restrictions/export/{format}', [IpRestrictionController::class, 'export'])
    ->where('format', 'csv|json')
    ->name('ip-restrictions.export');

Route::get('/ip-restrictions/logs/export', [IpRestrictionController::class, 'exportLogs'])
    ->name('ip-restrictions.logs.export');

Route::delete('/ip-restrictions/logs/clear', [IpRestrictionController::class, 'clearLogs'])
    ->name('ip-restrictions.logs.clear');

Route::patch('/ip-restrictions/{ipRestriction}/activate', [IpRestrictionController::class, 'activate'])
    ->name('ip-restrictions.activate');

Route::patch('/ip-restrictions/{ipRestriction}/deactivate', [IpRestrictionController::class, 'deactivate'])
    ->name('ip-restrictions.deactivate');

Route::delete('/ip-restrictions/{ipRestriction}', [IpRestrictionController::class, 'destroy'])
    ->name('ip-restrictions.destroy');

Route::get('/ip-restrictions/dark-mode/toggle', [IpRestrictionController::class, 'toggleDarkMode'])
    ->name('ip-restrictions.dark-mode.toggle');

Route::middleware(['blockIP'])->get('/protected', function () {
    return response()->json([
        'success' => true,
        'message' => 'Your IP address is allowed to access this protected page.',
        'ip_address' => request()->ip(),
    ]);
});
