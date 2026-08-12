<?php

use Illuminate\Support\Facades\Route;
use Modules\SchoolManagement\Http\Controllers\CampusController;
use Modules\SchoolManagement\Http\Controllers\DashboardController;
use Modules\SchoolManagement\Http\Controllers\DepartmentController;
use Modules\SchoolManagement\Http\Controllers\FeeController;
use Modules\SchoolManagement\Http\Controllers\FeeReportController;
use Modules\SchoolManagement\Http\Controllers\SchoolClassController;
use Modules\SchoolManagement\Http\Controllers\SchoolStaffController;
use Modules\SchoolManagement\Http\Controllers\SectionController;
use Modules\SchoolManagement\Http\Controllers\StudentController;
use Modules\SchoolManagement\Http\Controllers\TeacherController;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/school')
    ->as('school.')
    ->group(function () {
        Route::get('/', fn () => redirect()->route('school.dashboard'));
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        // Ajax data endpoints (must be before resource routes)
        Route::get('campuses/data', [CampusController::class,      'data'])->name('campuses.data');
        Route::get('departments/data', [DepartmentController::class,   'data'])->name('departments.data');
        Route::get('classes/data', [SchoolClassController::class,  'data'])->name('classes.data');
        Route::get('sections/data', [SectionController::class,      'data'])->name('sections.data');
        Route::get('teachers/data', [TeacherController::class,      'data'])->name('teachers.data');
        Route::get('students/data', [StudentController::class,      'data'])->name('students.data');
        Route::patch('students/{student}/para-quantity', [StudentController::class, 'updateParaQuantity'])->name('students.para-quantity.update');

        Route::resource('campuses', CampusController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('classes', SchoolClassController::class);
        Route::resource('sections', SectionController::class);
        Route::resource('teachers', TeacherController::class);
        Route::resource('students', StudentController::class);
        Route::resource('staff', SchoolStaffController::class)->except(['show']);

        // Fees Management
        Route::prefix('fees')->name('fees.')->group(function () {
            Route::get('/', [FeeController::class, 'index'])->name('index');
            Route::get('/collect', [FeeController::class, 'create'])->name('create');
            Route::post('/collect', [FeeController::class, 'store'])->name('store');
            Route::get('/search-student', [FeeController::class, 'searchStudent'])->name('search-student');

            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [FeeReportController::class, 'index'])->name('index');
                Route::get('/department', [FeeReportController::class, 'departmentSummary'])->name('department');
                Route::get('/class', [FeeReportController::class, 'classSummary'])->name('class');
            });

            Route::get('/{feePayment}', [FeeController::class, 'show'])->name('show');
        });
    });
