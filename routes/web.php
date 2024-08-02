<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\InitRootController;
use App\Http\Controllers\Auth\LINENotifySetupController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\Auth\TwoFactorsChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PersonalAccessTokenController;
use App\Http\Controllers\RevokeServiceRequestFormController;
use App\Http\Controllers\ServiceRequestFormController;
use Illuminate\Support\Facades\Route;

// register
Route::controller(RegisterUserController::class)
    ->prefix('register')
    ->name('register')
    ->middleware(['guest'])
    ->group(function () {
        Route::get('/', 'create');
        Route::post('/', 'store')
            ->name('.store');
    });

// auth
Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->middleware(['guest'])
    ->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest'])
    ->name('login.store');
Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');

// 2FA
Route::get('/2fa', [TwoFactorsChallengeController::class, 'create'])
    ->middleware(['auth'])
    ->name('2fa');
Route::post('/2fa', [TwoFactorsChallengeController::class, 'store'])
    ->middleware(['auth'])
    ->name('2fa.store');

// LINE notify
Route::controller(LINENotifySetupController::class)
    ->name('line-notify')
    ->group(function () {
        Route::get('/line-notify-auth', 'create')
            ->middleware(['auth'])
            ->name('.auth');
        Route::get('/line-notify-callback', 'store')
            ->name('.callback');
    });

// Dashboard
Route::get('/', DashboardController::class)
    ->middleware(['auth', '2fa-passed'])
    ->name('dashboard');

// Service Request Form
Route::patch('/service-request-forms/{form}/revoke', RevokeServiceRequestFormController::class)
    ->middleware(['auth', 'line-notify.enabled', '2fa-passed', 'can:revoke,form'])
    ->name('service-request-forms.revoke');
Route::controller(ServiceRequestFormController::class)
    ->prefix('service-request-forms')
    ->middleware(['auth', 'line-notify.enabled', '2fa-passed'])
    ->name('service-request-forms')
    ->group(function () {
        Route::get('/', 'index')
            ->name('');
        Route::post('/', 'store')
            ->name('.store');
        Route::get('/{form}/response', 'edit')
            ->middleware(['can:response,form'])
            ->name('.edit');
        Route::patch('/{form}', 'update')
            ->middleware(['can:response,form'])
            ->name('.update');
        Route::delete('/{form}', 'destroy')
            ->middleware(['can:cancel,form'])
            ->name('.destroy');
    });

// app token
Route::post('/app-tokens', [PersonalAccessTokenController::class, 'store'])
    ->middleware(['auth', '2fa-passed', 'can:create_app'])
    ->name('app-tokens.store');
Route::delete('/app-tokens/{token}', [PersonalAccessTokenController::class, 'destroy'])
    ->middleware(['auth', '2fa-passed', 'can:destroy,token'])
    ->name('app-tokens.destroy');

// init root
Route::get('/init-root/{code}', InitRootController::class)
    ->middleware(['auth']);

Route::post('/lab-pending', \App\Http\Controllers\API\LabPendingReportController::class)
    ->name('api.lab-pending');

Route::post('/lab-recently', \App\Http\Controllers\API\LabRecentlyOrderController::class)
    ->name('api.lab-recently');

Route::post('/lab-from-ref-no', [\App\Http\Controllers\API\LabResultController::class, 'fromRefNo'])
    ->name('api.lab-from-ref-no');

Route::post('/lab-from-service-id', [\App\Http\Controllers\API\LabResultController::class, 'fromServiceId'])
    ->name('api.lab-from-service-id');

Route::post('/lab-from-item-code', [\App\Http\Controllers\API\LabResultController::class, 'fromItemCode'])
    ->name('api.lab-from-item-code');

Route::post('/lab-from-item-all', [\App\Http\Controllers\API\LabResultController::class, 'fromItemCodeAllResult'])
    ->name('api.lab-from-item-all');
