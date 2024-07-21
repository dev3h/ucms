<?php

use App\Http\Controllers\Backend\ActionController;
use App\Http\Controllers\Backend\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Backend\AuditLogController;
use App\Http\Controllers\Backend\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Backend\Auth\ResetPasswordController;
use App\Http\Controllers\Backend\ChangePasswordController;
use App\Http\Controllers\Backend\IntegrationSocialiteController;
use App\Http\Controllers\Backend\ModuleController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SubSystemController;
use App\Http\Controllers\Backend\SystemController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');


Route::prefix("admin")->as("admin.api.")->group(function () {
    Route::middleware(['guest'])->group(function () {
        Route::post('/login', [AuthenticatedSessionController::class, 'handleLogin'])->name('login.handle');

        Route::post('/send-mail-reset-password', [ResetPasswordController::class, 'sendMailResetPassword'])
            ->name('send-mail-reset-password');
        Route::post('/update-password', [ResetPasswordController::class, 'passwordResetUpdate'])->name('reset-password.update');
    });

    Route::middleware(['auth', 'type_admin_check'])->group(function () {
        Route::get('me', [AuthenticatedSessionController::class, 'me'])->name('me');
        Route::post('/change-password-first', [AuthenticatedSessionController::class, 'changePasswordFirst'])->name('password-first.change');

        Route::apiResource('/system', SystemController::class);
        Route::apiResource('/subsystem', SubSystemController::class);
        Route::get('/subsystem/{id}/all-module', [SubSystemController::class, 'getAllModuleOfSubSystem'])->name('subsystem.all-module');
        Route::get('/subsystem/{id}/rest-module', [SubSystemController::class, 'restModule'])->name('subsystem.rest-module');
        Route::post('/subsystem/{id}/add-extra-module', [SubSystemController::class, 'addExtraModule'])->name('subsystem.add-extra');
        Route::delete('/subsystem/{id}/remove-module/{module_id}', [SubSystemController::class, 'removeModule'])->name('subsystem.remove-module');

        Route::apiResource('/module', ModuleController::class);
        Route::get('/module/{id}/all-action', [ModuleController::class, 'getAllActionOfModule'])->name('module.all-action');
        Route::get('/module/{id}/rest-action', [ModuleController::class, 'restAction'])->name('module.rest-action');
        Route::post('/module/{id}/add-extra-action', [ModuleController::class, 'addExtraAction'])->name('module.add-extra');
        Route::delete('/module/{id}/remove-action/{action_id}', [ModuleController::class, 'removeAction'])->name('module.remove-action');
        Route::get('/module/{id}/all-subsystem', [ModuleController::class, 'getAllSubSystemOfModule'])->name('module.all-subsystem');
        Route::get('/module/{id}/rest-subsystem', [ModuleController::class, 'restSubSystem'])->name('module.rest-subsystem');
        Route::post('/module/{id}/add-extra-subsystem', [ModuleController::class, 'addExtraSubSystem'])->name('module.add-extra-subsystem');
        Route::delete('/module/{id}/remove-subsystem/{subsystem_id}', [ModuleController::class, 'removeSubSystem'])->name('module.remove-subsystem');

        Route::apiResource('/action', ActionController::class);
        Route::apiResource('/role', RoleController::class);
        Route::apiResource('/permission', PermissionController::class);
        Route::apiResource('/user', UserController::class);
        Route::get('/user/{id}/all-permission', [UserController::class, 'getAllPermissionOfUser'])->name('user.all-permission');
        Route::get('/user/{id}/user-logs', [UserController::class, 'getUserLogs'])->name('user.user-logs');
        Route::get('/user/{user_id}/role/{role_id}/all-permission', [UserController::class, 'getAllPermissionOfUserByRole'])->name('user.role.all-permission');
        Route::put('/user/{id}/ignore-permission-for-user-role', [UserController::class, 'ignorePermissionForUserRole'])->name('user.ignore-permission-for-user-role');

        Route::get('/role/get/all-permission', [RoleController::class, 'getAllPermission'])->name('role.get-all-permission');
        Route::get('/role/{id}/template-permission', [PermissionController::class, 'templatePermission'])->name('role.template-permission');
        Route::put('/role/{id}/update-permission', [PermissionController::class, 'updateTemplatePermission'])->name('role.update-template-permission');
        Route::post('/role/store', [RoleController::class, 'store'])->name('role.store');
        Route::get('/user/{id}/template-permission', [PermissionController::class, 'templatePermissionUser'])->name('user.template-permission');
        Route::get('/role/{id}/all-user', [RoleController::class, 'getAllUserOfRole'])->name('role.all-user');
        Route::post('/role/{id}/assign-user', [RoleController::class, 'assignUser'])->name('role.assign-user');
        Route::delete('/role/{id}/revoked-user/{user_id}', [RoleController::class, 'revokeUser'])->name('role.revoke-user');
        Route::get('/role/{id}/all-permission', [RoleController::class, 'getAllPermissionOfRole'])->name('role.all-permission');
        Route::delete('/role/{id}/revoke-permission/{permission_id}', [RoleController::class, 'revokePermission'])->name('role.revoke-permission');
        Route::get('/role/{id}/rest-permission', [RoleController::class, 'restPermission'])->name('role.rest-permission');
        Route::post('/role/{id}/assign-permission', [RoleController::class, 'assignPermission'])->name('role.assign-permission');

        Route::get('/code-for-permission', [PermissionController::class, 'getCodeForPermission'])->name('permission.code-for-permission');
        // integration with socialite
        Route::get('/integration-socialites', [IntegrationSocialiteController::class, 'getAllIntegrationSocial'])->name('get-all-integration-socialite');
        Route::delete('/unlink-socialite/{provider_id}', [IntegrationSocialiteController::class, 'unlinkIntegrationSocial'])->name('unlink-integration-socialite');

        // change password
        Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('change-password');
        // Notification user
        Route::prefix('/notification')->as('notification.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
            Route::post('/', [NotificationController::class, 'store'])->name('store');
            Route::put('/{id}', [NotificationController::class, 'update'])->name('update');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('delete');
            Route::post('/get-member-can-add', [NotificationController::class, 'membersCanAdd'])
                ->name('member-can-add');
        });

        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
    });
});

Route::prefix("app")->as("app.api.")->group(function () {
    Route::middleware(['guest'])->group(function () {
        Route::post('/login', [AuthenticatedSessionController::class, 'handleLogin'])->name('login.handle');

        Route::post('/send-mail-reset-password', [ResetPasswordController::class, 'sendMailResetPassword'])
            ->name('send-mail-reset-password');
        Route::post('/update-password', [ResetPasswordController::class, 'passwordResetUpdate'])->name('reset-password.update');
    });

    Route::middleware(['auth'])->group(function () {
        Route::post('/change-password-first', [AuthenticatedSessionController::class, 'changePasswordFirst'])->name('password-first.change');

        Route::get('/code-for-permission', [PermissionController::class, 'getCodeForPermission'])->name('permission.code-for-permission');
        // integration with socialite
        Route::get('/integration-socialites', [IntegrationSocialiteController::class, 'getAllIntegrationSocial'])->name('get-all-integration-socialite');
        Route::delete('/unlink-socialite/{provider_id}', [IntegrationSocialiteController::class, 'unlinkIntegrationSocial'])->name('unlink-integration-socialite');

        // change password
        Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('change-password');

    });
});

//// api send otp and login user
//Route::post('/send-otp-login', [LoginController::class, 'sendOtpLogin']);
//Route::post('/check-otp-login', [LoginController::class, 'checkOtpLogin']);
//
//// api send otp and register user
//Route::post('/send-otp-register', [RegisterController::class, 'sendOtpRegister']);
//Route::post('/check-otp-register', [RegisterController::class, 'checkOtpRegister']);



