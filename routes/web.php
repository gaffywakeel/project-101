<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\WebHook\CoinController;
use App\Http\Middleware\AuthorizeControlPanel;
use Illuminate\Support\Facades\Route;
use Spatie\Csp\AddCspHeaders;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::post('demo-login', [LoginController::class, 'demoLogin'])->name('demo-login');
    Route::post('register', [RegisterController::class, 'register'])->name('register');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('reset-password')->name('reset-password.')->group(function () {
        Route::post('reset', [ResetPasswordController::class, 'reset'])->name('reset');
        Route::post('send-email-code', [ResetPasswordController::class, 'sendEmailCode'])->name('send-email-code');
        Route::post('request-token', [ResetPasswordController::class, 'requestToken'])->name('request-token');
    });
});

Route::prefix('webhook')->name('webhook.')->group(function () {
    Route::prefix('coin/{identifier}')->name('coin.')->group(function () {
        Route::post('transaction', [CoinController::class, 'handleTransaction'])->name('transaction');
    });
});

Route::prefix('gateway/{order}')->name('gateway.')->group(function () {
    Route::get('callback', [GatewayController::class, 'handleReturn'])->name('return');
    Route::post('callback', [GatewayController::class, 'handleNotify'])->name('notify');
});

Route::name('installer.')->group(function () {
    Route::get('installer/{any?}', [InstallerController::class, 'view'])->where('any', '.*');
    Route::post('installer/register', [InstallerController::class, 'register'])->name('register');
    Route::post('installer/install', [InstallerController::class, 'install'])->name('install');
});

Route::middleware([AddCspHeaders::class, 'installer.require'])->group(function () {
    Route::get('auth/{any?}', [AppController::class, 'auth'])->where('any', '.*')->middleware('guest')->name('auth');
    Route::get('admin/{any?}', [AppController::class, 'admin'])->where('any', '.*')->middleware(AuthorizeControlPanel::class)->name('admin');
    Route::get('{any?}', [AppController::class, 'index'])->where('any', '.*')->name('index');
});
