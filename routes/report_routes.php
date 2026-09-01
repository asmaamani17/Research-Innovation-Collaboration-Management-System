<?php

use App\Http\Controllers\Admin\ReportController;

// Report Module Routes
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/faculty-performance', [ReportController::class, 'facultyPerformance'])->name('reports.faculty-performance');
Route::get('/reports/award-statistics', [ReportController::class, 'awardStatistics'])->name('reports.award-statistics');
Route::get('/reports/staff-performance', [ReportController::class, 'staffPerformance'])->name('reports.staff-performance');
Route::get('/reports/event-participation', [ReportController::class, 'eventParticipation'])->name('reports.event-participation');

// Export Routes
Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');

// API Routes for Charts
Route::get('/api/reports/chart-data', [ReportController::class, 'getChartData'])->name('reports.api.chart-data');
