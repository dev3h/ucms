<?php

use App\Http\Controllers\Backend\IntegrationSocialiteController;
use App\Http\Controllers\Backend\SocialiteController;
use App\Http\Controllers\Frontend\ActionController;
use App\Http\Controllers\Frontend\ApplicationUserController;
use App\Http\Controllers\Frontend\AuditLogController;
use App\Http\Controllers\Frontend\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Frontend\Auth\ResetPasswordController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\ModuleController;
use App\Http\Controllers\Frontend\NotificationController;
use App\Http\Controllers\Frontend\PermissionController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\RoleController;
use App\Http\Controllers\Frontend\SubSystemController;
use App\Http\Controllers\Frontend\SystemController;
use App\Http\Controllers\Frontend\UserController;
use Illuminate\Support\Facades\Route;

//Frontend
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

//    Route::middleware([CustomJWTAuth::class])
    Route::middleware(['auth', 'type_admin_check'])->group(function () {

        Route::get('/change-password-first', [AuthenticatedSessionController::class, 'formChangePasswordFirst'])->name('password-first.form');
        Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');

        Route::resource('system', SystemController::class);
        Route::resource('subsystem', SubSystemController::class);
        Route::resource('module', ModuleController::class);
        Route::resource('action', ActionController::class);
        Route::resource('role', RoleController::class);
        Route::resource('permission', PermissionController::class);
        Route::resource('user', UserController::class);
        Route::resource('audit-log', AuditLogController::class);

        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

        // integration
        Route::get('/integration/redirect/{provider}', [IntegrationSocialiteController::class, 'redirectIntegrationSocial'])->name('integration.socialite.redirect');
        Route::get('/integration/callback/{provider}', [IntegrationSocialiteController::class, 'callbackIntegrationSocial'])->name('integration.socialite.callback');

       // application user
        Route::get('/application', [ApplicationUserController::class, 'index'])->name('application');

        // dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Notification user
        Route::prefix('/notification')->as('notification.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/create', [NotificationController::class, 'create'])->name('create');
            Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [NotificationController::class, 'update'])->name('update');
        });
    });

});

Route::get('/', function () {
   return redirect()->route('admin.login.form');
});
require_once __DIR__ . '/fortify.php';
Route::get('admin/reset-password/{token}',  [ResetPasswordController::class, 'passwordReset'])->name('password.reset')
    ->middleware(['guest', 'signed', 'throttle:6,1']);





