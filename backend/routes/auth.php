<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UnsubscribeBirthdateController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Middleware\CheckTokenExpiry;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [LoginController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest', 'throttle:5,1')
    ->name('password.email');

Route::post('/profile', [ProfileController::class, 'show'])
    ->middleware([CheckTokenExpiry::class])
    ->name('profile.show');

Route::put('/profile', [ProfileController::class, 'update'])
    ->middleware([CheckTokenExpiry::class])
    ->name('profile.update');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::put('/change-password', [PasswordChangeController::class, 'update'])
    ->middleware([CheckTokenExpiry::class])
    ->name('password.update');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');

Route::post('/unsubscribe-birthdate', [UnsubscribeBirthdateController::class, 'unsubscribe_birthdate'])
    ->name('unsubscribe-birthdate');
