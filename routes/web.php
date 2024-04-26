<?php

use App\Http\Controllers\Backend\IntegrationSocialiteController;
use App\Http\Controllers\Backend\SocialiteController;
use App\Http\Controllers\Frontend\ActionController;
use App\Http\Controllers\Frontend\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Frontend\Auth\ResetPasswordController;
use App\Http\Controllers\Frontend\ModuleController;
use App\Http\Controllers\Frontend\PermissionController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\RoleController;
use App\Http\Controllers\Frontend\SubSystemController;
use App\Http\Controllers\Frontend\SystemController;
use App\Http\Controllers\Frontend\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// //Frontend
Route::prefix("admin/")->as("admin.")->group(function () {
    Route::middleware(['guest'])->group(function () {
        Route::get('/login', [AuthenticatedSessionController::class, 'formLogin'])->name('login.form');
        Route::get('/change-password-first', [AuthenticatedSessionController::class, 'formChangePasswordFirst'])->name('password-first.form');
        Route::get('/forgot-password', [ResetPasswordController::class, 'formForgotPassword'])
            ->name('forgot-password.form');

        Route::get('/confirm-forgot-password', [ResetPasswordController::class, 'confirmForgotPassword'])
            ->name('form-confirm-forgot-password');

        // Auth SNS for user
        Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirectSocial'])->name('socialite.redirect');
        Route::get('/callback/{provider}', [SocialiteController::class, 'callbackSocial'])->name('socialite.callback');

        Route::get('two-factor-challenge', [AuthenticatedSessionController::class, 'formTwoFactorChallenge'])->name('two-factor-challenge.form');

    });

    Route::middleware(['auth'])->group(function () {

        Route::get('/change-password-first', [AuthenticatedSessionController::class, 'formChangePasswordFirst'])->name('password-first.form');
        Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');

        Route::resource('system', SystemController::class);
        Route::resource('subsystem', SubSystemController::class);
        Route::resource('module', ModuleController::class);
        Route::resource('action', ActionController::class);
        Route::resource('role', RoleController::class);
        Route::resource('permission', PermissionController::class);
        Route::resource('user', UserController::class);

        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

        // integration
        Route::get('/integration/redirect/{provider}', [IntegrationSocialiteController::class, 'redirectIntegrationSocial'])->name('integration.socialite.redirect');
        Route::get('/integration/callback/{provider}', [IntegrationSocialiteController::class, 'callbackIntegrationSocial'])->name('integration.socialite.callback');
    });

});

require_once __DIR__ . '/fortify.php';
Route::get('admin/reset-password/{token}', [ResetPasswordController::class, 'passwordReset'])->name('password.reset')
    ->middleware(['guest', 'signed', 'throttle:6,1']);




