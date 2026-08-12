<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\MadrasaFunds\Http\Controllers\DashboardController;
use Modules\MadrasaFunds\Http\Controllers\DepartmentController;
use Modules\MadrasaFunds\Http\Controllers\FundController;
use Modules\MadrasaFunds\Http\Controllers\ReceiptController;
use Modules\MadrasaFunds\Http\Controllers\ReportController;
use Modules\MadrasaFunds\Http\Controllers\StudentController;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/madrasafunds')
    ->as('admin.madrasafunds.')
    ->group(function () {
        Route::get('/', fn () => redirect()->route('admin.madrasafunds.dashboard'));
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        // Ajax data endpoints (must be registered before resource routes).
        Route::get('funds/data', [FundController::class,       'data'])->name('funds.data');
        Route::get('departments/data', [DepartmentController::class, 'data'])->name('departments.data');
        Route::get('students/data', [StudentController::class,    'data'])->name('students.data');
        Route::get('receipts/data', [ReceiptController::class,    'data'])->name('receipts.data');

        // Receipt extras (before the resource so they are not captured by {receipt}).
        Route::get('receipts/search-student', [ReceiptController::class, 'searchStudent'])->name('receipts.search-student');
        Route::get('receipts/{receipt}/print', [ReceiptController::class, 'print'])->name('receipts.print');
        Route::patch('receipts/{receipt}/cancel', [ReceiptController::class, 'cancel'])->name('receipts.cancel');

        Route::resource('funds', FundController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('students', StudentController::class);
        Route::resource('receipts', ReceiptController::class);

        // Reports.
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/fund', [ReportController::class, 'fund'])->name('fund');
            Route::get('/department', [ReportController::class, 'department'])->name('department');
            Route::get('/student', [ReportController::class, 'student'])->name('student');
            Route::get('/food', [ReportController::class, 'food'])->name('food');
            Route::get('/consolidated', [ReportController::class, 'consolidated'])->name('consolidated');
        });
    });
