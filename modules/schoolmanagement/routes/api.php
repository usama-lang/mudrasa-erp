<?php

use Illuminate\Support\Facades\Route;
use Modules\SchoolManagement\Http\Controllers\SchoolManagementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('schoolmanagement', SchoolManagementController::class)->names('schoolmanagement');
});
