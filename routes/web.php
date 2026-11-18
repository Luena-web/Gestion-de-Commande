<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LoginController;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('api/v1')->group(function () {
    Route::post('/login', [LoginController::class, 'loginWeb'])
        ->name('login.web');

    Route::post('/logout', [LoginController::class, 'logoutWeb'])
        ->middleware('auth:web')
        ->name('logout.web');
});