<?php

use App\Http\Controllers\Backend\ActionController;
use App\Http\Controllers\Backend\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Backend\Auth\ResetPasswordController;
use App\Http\Controllers\Backend\ChangePasswordController;
use App\Http\Controllers\Backend\IntegrationSocialiteController;
use App\Http\Controllers\Backend\ModuleController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SubSystemController;
use App\Http\Controllers\Backend\SystemController;
use App\Http\Controllers\Backend\TwoFactorAuthController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix("admin")->as("admin.api.")->group(function () {
    Route::middleware(['guest'])->group(function () {
        Route::post('/login', [AuthenticatedSessionController::class, 'handleLogin'])->name('login.handle');

        Route::post('/send-mail-reset-password', [ResetPasswordController::class, 'sendMailResetPassword'])
            ->name('send-mail-reset-password');
        Route::post('/update-password', [ResetPasswordController::class, 'passwordResetUpdate'])->name('reset-password.update');
    });

    Route::middleware(['auth'])->group(function () {
        Route::post('/two-factor-challenge', [AuthenticatedSessionController::class, 'twoFactorChallenge'])->name('two-factor-challenge');
        Route::post('/change-password-first', [AuthenticatedSessionController::class, 'changePasswordFirst'])->name('password-first.change');

        Route::apiResource('/system', SystemController::class);
        Route::apiResource('/subsystem', SubSystemController::class);
        Route::apiResource('/module', ModuleController::class);
        Route::apiResource('/action', ActionController::class);
        Route::apiResource('/role', RoleController::class);
        Route::apiResource('/permission', PermissionController::class);
        Route::apiResource('/user', UserController::class);
        Route::get('/role/{id}/template-permission', [PermissionController::class, 'templatePermission'])->name('role.template-permission');
        Route::put('/role/{id}/update-permission', [PermissionController::class, 'updateTemplatePermission'])->name('role.update-template-permission');
        Route::post('/role/store', [RoleController::class, 'store'])->name('role.store');
        Route::get('/user/{id}/template-permission', [PermissionController::class, 'templatePermissionUser'])->name('user.template-permission');


        // integration with socialite
        Route::get('/integration-socialites', [IntegrationSocialiteController::class, 'getAllIntegrationSocial'])->name('get-all-integration-socialite');
        Route::delete('/unlink-socialite/{provider_id}', [IntegrationSocialiteController::class, 'unlinkIntegrationSocial'])->name('unlink-integration-socialite');

        // change password
        Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('change-password');

        Route::post('/generate-2fa-secret', [TwoFactorAuthController::class, 'generate2faSecret'])->name('generate-2fa-secret');
    });

});
