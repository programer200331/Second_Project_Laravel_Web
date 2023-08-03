<?php

use App\Http\Controllers\Auth\WebAuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::fallback(function () {
    return view('not_found');
});

Route::view('/', 'welcome');

// Route::view('/home', 'cms.parent');

// Auth Requests
Route::prefix('cms/admin/auth')->middleware('guest:admin')->group(function () {
    Route::get('login', [WebAuthController::class, 'showLogin'])->name('cms.show-login');
    Route::post('login', [WebAuthController::class, 'login'])->name('cms.login');
    Route::get('forgot-password', [WebAuthController::class, 'forgotPassword'])->name('password-forgot');
    Route::post('forgot-password', [WebAuthController::class, 'sendResetEmail'])->name('password.reset-email');
    Route::get('reset-password/{token}', [WebAuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('reset-password', [WebAuthController::class, 'resetPassword'])->name('password.update');
});

Route::prefix('cms/admin/auth')->middleware('auth:admin')->group(function () {
    Route::get('verify-email', [WebAuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::get('verify/send-email', [WebAuthController::class, 'sendVerificationEmail'])->name('verification.send-email');
    Route::get('verify/{id}/{hash}', [WebAuthController::class, 'verify'])->name('verification.verify');
    Route::get('edit-password', [WebAuthController::class, 'editPassword'])->name('cms.edit-password');
    Route::put('update-password', [WebAuthController::class, 'updatePassword'])->name('cms.update-password');
    Route::get('logout', [WebAuthController::class, 'logout'])->name('cms.logout');
});

// index, create, store, show, edit, update, destroy (7 Methods <=> 7 Routes)
Route::prefix('cms/admin')->middleware(['auth:admin','verified'])->group(function () {
    Route::resource('categories', CategoryController::class);
});

Route::prefix('cms/admin/maintenance')->group(function () {
    Route::get('down', function () {
        Artisan::call('down');
    });
    Route::get('up', function () {
        Artisan::call('up');
    });
});

// Route::view('/starter','cms.starter');
// Route::view('/demo','cms.demo');
// Route::view('/tables','cms.categories.index');

// Route::view('/cms/categories/index','cms.categories.index');