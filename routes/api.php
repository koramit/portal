<?php

use App\Http\Controllers\API\AdmissionController;
use App\Http\Controllers\API\AdmissionDischargeDateController;
use App\Http\Controllers\API\AdmissionTransferController;
use App\Http\Controllers\API\AuthenticateUserADFSController;
use App\Http\Controllers\API\AuthenticateUserController;
use App\Http\Controllers\API\COVID19PCRLabController;
use App\Http\Controllers\API\COVID19VaccinationController;
use App\Http\Controllers\API\GetUserController;
use App\Http\Controllers\API\ItemizeController;
use App\Http\Controllers\API\LabPendingReportController;
use App\Http\Controllers\API\LabRecentlyOrderController;
use App\Http\Controllers\API\LabResultController;
use App\Http\Controllers\API\PatientAdmissionController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\PatientRecentlyAdmissionController;
use App\Http\Controllers\API\WardAdmissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware('auth:sanctum')
    ->group(function () {
        Route::post('/user', GetUserController::class)
            ->middleware('ability:user:data')
            ->name('api.user');
        Route::post('/user-with-sensitive-data', GetUserController::class)
            ->middleware('abilities:user:data,user:sensitive-data')
            ->name('api.user-with-sensitive-data');
        Route::post('/authenticate', AuthenticateUserController::class)
            ->middleware('ability:user:authenticate')
            ->name('api.authenticate');
        Route::post('/authenticate-with-sensitive-data', AuthenticateUserController::class)
            ->middleware('ability:user:authenticate')
            ->name('api.authenticate-with-sensitive-data');
        Route::post('/authenticate-adfs', AuthenticateUserADFSController::class)
            ->middleware('ability:user:authenticate')
            ->name('api.authenticate-adfs');

        Route::post('/patient', PatientController::class)
            ->middleware('ability:patient:data')
            ->name('api.patient');
        Route::post('/patient-with-sensitive-data', PatientController::class)
            ->middleware('abilities:patient:data,patient:sensitive-data')
            ->name('api.patient-with-sensitive-data');

        Route::post('/patient-admissions', PatientAdmissionController::class)
            ->middleware('ability:patient:admissions')
            ->name('api.patient-admissions');
        Route::post('/patient-admissions-with-sensitive-data', PatientAdmissionController::class)
            ->middleware('abilities:patient:admissions,patient:sensitive-data')
            ->name('api.patient-admissions-with-sensitive-data');

        Route::post('/patient-recently-admission', PatientRecentlyAdmissionController::class)
            ->middleware('ability:patient:recently-admission')
            ->name('api.patient-recently-admission');
        Route::post('/patient-recently-admission-with-sensitive-data', PatientRecentlyAdmissionController::class)
            ->middleware('abilities:patient:recently-admission,patient:sensitive-data')
            ->name('api.patient-recently-admission-with-sensitive-data');

        Route::post('/admission', AdmissionController::class)
            ->middleware('ability:admission:data')
            ->name('api.admission');
        Route::post('/admission-with-sensitive-data', AdmissionController::class)
            ->middleware('abilities:admission:data,patient:sensitive-data')
            ->name('api.admission-with-sensitive-data');

        Route::post('/covid-19-vaccinations', COVID19VaccinationController::class)
            ->middleware('ability:covid19:vaccinations')
            ->name('api.covid-19-vaccinations');

        Route::post('/covid-19-pcr-labs', COVID19PCRLabController::class)
            ->middleware('ability:covid19:pcr-labs')
            ->name('api.covid-19-pcr-labs');

        Route::post('/ward-admissions', WardAdmissionController::class)
            ->middleware('ability:ward:admissions')
            ->name('api.ward-admissions');

        Route::post('/admission-discharge-date', AdmissionDischargeDateController::class)
            ->middleware('ability:ward:admissions')
            ->name('api.admission-discharge-date');

        Route::post('/admission-transfers', AdmissionTransferController::class)
            ->middleware('ability:ward:admissions')
            ->name('api.admission-transfers');

        Route::post('/itemize', ItemizeController::class)
            ->middleware('ability:item:master')
            ->name('api.itemize');

        Route::post('/lab-pending', LabPendingReportController::class)
            ->middleware('ability:lab:pending')
            ->name('api.lab-pending');

        Route::post('/lab-recently', LabRecentlyOrderController::class)
            ->middleware('ability:lab:results')
            ->name('api.lab-recently');

        Route::post('/lab-from-ref-no', [LabResultController::class, 'fromRefNo'])
            ->middleware('ability:lab:results')
            ->name('api.lab-from-ref-no');

        Route::post('/lab-from-service-id', [LabResultController::class, 'fromServiceId'])
            ->middleware('ability:lab:results')
            ->name('api.lab-from-service-id');

        Route::post('/lab-from-item-code', [LabResultController::class, 'fromItemCode'])
            ->middleware('ability:lab:results')
            ->name('api.lab-from-item-code');

        Route::post('/lab-from-item-all', [LabResultController::class, 'fromItemCodeAllResult'])
            ->middleware('ability:lab:results')
            ->name('api.lab-from-item-all');
    });
