<?php

use Illuminate\Support\Facades\Route;
use Modules\MadrasaFunds\Http\Controllers\MadrasaFundsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('madrasafunds', MadrasaFundsController::class)->names('madrasafunds');
});
