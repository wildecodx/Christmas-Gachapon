
<?php

use App\Http\Controllers\GachaponManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GachaponSetupController;
use App\Http\Controllers\GachaponPlayController;

// Welcome page — appears first
// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::get('/', function () {
    return 'Laravel is working on Render!';
});

// Setup page — opens after clicking Let's Get Started
Route::get('/setup', [GachaponSetupController::class, 'create'])
    ->name('gachapon.setup');

Route::post('/setup', [GachaponSetupController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('gachapon.setup.store');


// Godchild invitation page
Route::prefix('play/{token}')
    ->name('gachapon.')
    ->middleware('throttle:60,1')
    ->group(function () {
        Route::get('/', [GachaponPlayController::class, 'show'])
            ->name('play');

        Route::post('/verify-code', [
            GachaponPlayController::class,
            'verifyCode',
        ])->name('verify-code');

        Route::get('/machine', [
            GachaponPlayController::class,
            'machine',
        ])->name('machine');

        Route::post('/open', [
            GachaponPlayController::class,
            'open',
        ])->name('open');
    });

    
Route::get('/prize/{token}', [GachaponPlayController::class, 'prize'])
    ->middleware('throttle:60,1')
    ->where('token', '[A-Za-z0-9]{32,64}')
    ->name('gachapon.prize');

Route::prefix('manage/{token}')
    ->name('gachapon.manage.')
    ->middleware('throttle:30,1')
    ->group(function () {
        Route::get('/', [GachaponManagementController::class, 'show'])
            ->name('show');

        Route::get('/data', [GachaponManagementController::class, 'data'])
            ->name('data');

        Route::post('/gifts/{gift}/stock', [
            GachaponManagementController::class,
            'addStock',
        ])->whereNumber('gift')->name('gifts.stock');
    });