<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\KpiController;
use App\Http\Controllers\Admin\IpController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Authentication routes handled in routes/auth.php (use named route 'login')

// Super Admin Dashboard Routes
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/data', [SuperAdminController::class, 'getDashboardData'])->name('dashboard.data');
});

// Workspace Switcher Routes
Route::middleware(['auth', 'superadmin'])->prefix('workspace')->name('workspace.')->group(function () {
    Route::get('/switch/{workspace}', [SuperAdminController::class, 'switchWorkspace'])->name('switch');
});

Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware(['auth'])->name('admin.dashboard');

Route::get('/admin/users/staff', [\App\Http\Controllers\Admin\StaffController::class, 'index'])->middleware(['auth'])->name('admin.staff.index');
Route::post('/admin/users/staff', [\App\Http\Controllers\Admin\StaffController::class, 'store'])->middleware(['auth'])->name('admin.staff.store');
Route::put('/admin/users/staff/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'update'])->middleware(['auth'])->name('admin.staff.update');
Route::delete('/admin/users/staff/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'destroy'])->middleware(['auth'])->name('admin.staff.destroy');
Route::get('/admin/users/staff/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'show'])->middleware(['auth'])->name('admin.staff.show');

Route::get('/admin/faculty', [\App\Http\Controllers\Admin\FacultyController::class, 'index'])->middleware(['auth'])->name('admin.faculty.index');
Route::post('/admin/faculty', [\App\Http\Controllers\Admin\FacultyController::class, 'store'])->middleware(['auth'])->name('admin.faculty.store');
Route::put('/admin/faculty/{id}', [\App\Http\Controllers\Admin\FacultyController::class, 'update'])->middleware(['auth'])->name('admin.faculty.update');
Route::delete('/admin/faculty/{id}', [\App\Http\Controllers\Admin\FacultyController::class, 'destroy'])->middleware(['auth'])->name('admin.faculty.destroy');
Route::get('/admin/faculty/{id}', [\App\Http\Controllers\Admin\FacultyController::class, 'show'])->middleware(['auth'])->name('admin.faculty.show');
Route::get('/admin/faculty/{id}/staff', [\App\Http\Controllers\Admin\FacultyController::class, 'staff'])->middleware(['auth'])->name('admin.faculty.staff');
Route::get('/admin/faculty/{id}/awards', [\App\Http\Controllers\Admin\FacultyController::class, 'awards'])->middleware(['auth'])->name('admin.faculty.awards');
Route::get('/admin/faculty/{id}/stats', [\App\Http\Controllers\Admin\FacultyController::class, 'stats'])->middleware(['auth'])->name('admin.faculty.stats');

Route::get('/admin/projects', [\App\Http\Controllers\Admin\ProjectController::class, 'index'])->middleware(['auth'])->name('admin.projects.index');
Route::get('/admin/projects/create', [\App\Http\Controllers\Admin\ProjectController::class, 'create'])->middleware(['auth'])->name('admin.projects.create');
Route::post('/admin/projects', [\App\Http\Controllers\Admin\ProjectController::class, 'store'])->middleware(['auth'])->name('admin.projects.store');
Route::get('/admin/projects/{id}/edit', [\App\Http\Controllers\Admin\ProjectController::class, 'edit'])->middleware(['auth'])->name('admin.projects.edit');
Route::put('/admin/projects/{id}', [\App\Http\Controllers\Admin\ProjectController::class, 'update'])->middleware(['auth'])->name('admin.projects.update');
Route::delete('/admin/projects/{id}', [\App\Http\Controllers\Admin\ProjectController::class, 'destroy'])->middleware(['auth'])->name('admin.projects.destroy');
Route::get('/admin/projects/{id}', [\App\Http\Controllers\Admin\ProjectController::class, 'show'])->middleware(['auth'])->name('admin.projects.show');
Route::get('/admin/projects/{id}/staff', [\App\Http\Controllers\Admin\ProjectController::class, 'staff'])->middleware(['auth'])->name('admin.projects.staff');
Route::post('/admin/projects/{id}/staff', [\App\Http\Controllers\Admin\ProjectController::class, 'addStaff'])->middleware(['auth'])->name('admin.projects.addStaff');
Route::delete('/admin/projects/{projectId}/staff/{staffId}', [\App\Http\Controllers\Admin\ProjectController::class, 'removeStaff'])->middleware(['auth'])->name('admin.projects.removeStaff');
Route::get('/admin/projects/{id}/stats', [\App\Http\Controllers\Admin\ProjectController::class, 'stats'])->middleware(['auth'])->name('admin.projects.stats');
Route::get('/admin/staff/{staffId}/projects', [\App\Http\Controllers\Admin\ProjectController::class, 'byStaff'])->middleware(['auth'])->name('admin.staff.projects');
Route::get('/admin/faculty/{facultyId}/projects', [\App\Http\Controllers\Admin\ProjectController::class, 'byFaculty'])->middleware(['auth'])->name('admin.faculty.projects');

Route::get('/admin/events', [\App\Http\Controllers\Admin\CompetitionController::class, 'index'])->middleware(['auth'])->name('admin.events');
Route::post('/admin/events', [\App\Http\Controllers\Admin\CompetitionController::class, 'store'])->middleware(['auth'])->name('admin.event.store');
Route::get('/admin/events/create', [\App\Http\Controllers\Admin\CompetitionController::class, 'create'])->middleware(['auth'])->name('admin.event.create');
Route::get('/admin/events/{id}/edit', [\App\Http\Controllers\Admin\CompetitionController::class, 'edit'])->middleware(['auth'])->name('admin.event.edit');
Route::put('/admin/events/{id}', [\App\Http\Controllers\Admin\CompetitionController::class, 'update'])->middleware(['auth'])->name('admin.event.update');
Route::delete('/admin/events/{id}', [\App\Http\Controllers\Admin\CompetitionController::class, 'destroy'])->middleware(['auth'])->name('admin.event.destroy');
Route::get('/admin/events/{id}', [\App\Http\Controllers\Admin\CompetitionController::class, 'show'])->middleware(['auth'])->name('admin.event.show');

Route::get('/admin/awards', [\App\Http\Controllers\Admin\AwardController::class, 'index'])->middleware(['auth'])->name('admin.awards');
Route::get('/admin/awards/create', [\App\Http\Controllers\Admin\AwardController::class, 'create'])->middleware(['auth'])->name('admin.awards.create');
Route::get('/admin/awards/dashboard', [\App\Http\Controllers\Admin\AwardController::class, 'dashboard'])->middleware(['auth'])->name('admin.awards.dashboard');
Route::get('/admin/awards/import', [\App\Http\Controllers\Admin\AwardController::class, 'import'])->middleware(['auth'])->name('admin.awards.import');
Route::get('/admin/awards/reports', [\App\Http\Controllers\Admin\AwardController::class, 'reports'])->middleware(['auth'])->name('admin.awards.reports');
Route::get('/admin/awards/{id}', [\App\Http\Controllers\Admin\AwardController::class, 'show'])->middleware(['auth'])->name('admin.awards.show');
Route::get('/admin/awards/{id}/edit', [\App\Http\Controllers\Admin\AwardController::class, 'edit'])->middleware(['auth'])->name('admin.awards.edit');
Route::get('/api/awards/chart-data', [\App\Http\Controllers\Admin\AwardController::class, 'chartData'])->middleware(['auth'])->name('api.awards.chart-data');
Route::post('/admin/awards', [\App\Http\Controllers\Admin\AwardController::class, 'store'])->middleware(['auth'])->name('admin.awards.store');
Route::put('/admin/awards/{id}', [\App\Http\Controllers\Admin\AwardController::class, 'update'])->middleware(['auth'])->name('admin.awards.update');
Route::delete('/admin/awards/{id}', [\App\Http\Controllers\Admin\AwardController::class, 'destroy'])->middleware(['auth'])->name('admin.awards.destroy');

// IP Management Routes
Route::middleware(['auth'])->prefix('admin/ip')->name('admin.ip.')->group(function () {
    Route::get('/', [IpController::class, 'index'])->name('index');
    Route::get('/create', [IpController::class, 'create'])->name('create');
    Route::post('/', [IpController::class, 'store'])->name('store');
    Route::get('/dashboard', [IpController::class, 'dashboard'])->name('dashboard');
    Route::get('/import', [IpController::class, 'import'])->name('import');
    Route::get('/reports', [IpController::class, 'reports'])->name('reports');
    Route::get('/export', [IpController::class, 'export'])->name('export');
    Route::get('/{id}', [IpController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [IpController::class, 'edit'])->name('edit');
    Route::put('/{id}', [IpController::class, 'update'])->name('update');
    Route::delete('/{id}', [IpController::class, 'destroy'])->name('destroy');
});

// API Routes for IP Charts
Route::get('/api/ip/chart-data', [IpController::class, 'chartData'])->middleware(['auth'])->name('api.ip.chart-data');

// User Management Routes (Superadmin only)
Route::middleware(['auth', 'superadmin'])->prefix('admin/users')->name('admin.users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/modal', [UserController::class, 'modal'])->name('modal');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});

// Import Routes
Route::middleware(['auth'])->prefix('admin/import')->name('admin.import.')->group(function () {
    Route::get('/', [ImportController::class, 'index'])->name('index');
    Route::get('/template/{type}', [ImportController::class, 'downloadTemplate'])->name('template');
    Route::post('/', [ImportController::class, 'import'])->name('import');
});

// KPI Dashboard Routes
Route::middleware(['auth'])->prefix('admin/kpi')->name('admin.kpi.')->group(function () {
    Route::get('/', [KpiController::class, 'index'])->name('index');
    Route::get('/edit', [KpiController::class, 'edit'])->name('edit');
    Route::get('/data', [KpiController::class, 'getData'])->name('data');
    Route::post('/update', [KpiController::class, 'updateKpi'])->name('updateKpi');
    Route::post('/update-phase', [KpiController::class, 'updatePhase'])->name('updatePhase');
    Route::get('/template/{type}', [KpiController::class, 'downloadTemplate'])->name('template');
    Route::get('/export', [KpiController::class, 'export'])->name('export');
});

// Report Module Routes
Route::get('/admin/reports', [ReportController::class, 'index'])->middleware(['auth'])->name('admin.reports');
Route::get('/admin/reports/faculty-performance', [ReportController::class, 'facultyPerformance'])->middleware(['auth'])->name('reports.faculty-performance');
Route::get('/admin/reports/award-statistics', [ReportController::class, 'awardStatistics'])->middleware(['auth'])->name('reports.award-statistics');
Route::get('/admin/reports/staff-performance', [ReportController::class, 'staffPerformance'])->middleware(['auth'])->name('reports.staff-performance');
Route::get('/admin/reports/event-participation', [ReportController::class, 'eventParticipation'])->middleware(['auth'])->name('reports.event-participation');

// Export Routes
Route::get('/admin/reports/export/excel', [ReportController::class, 'exportExcel'])->middleware(['auth'])->name('reports.export.excel');
Route::get('/admin/reports/export/csv', [ReportController::class, 'exportCsv'])->middleware(['auth'])->name('reports.export.csv');

// API Routes for Charts
Route::get('/api/reports/chart-data', [ReportController::class, 'getChartData'])->middleware(['auth'])->name('reports.api.chart-data');

Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

Route::get('/contact/admin', function () {
    return view('contact.admin');
})->name('contact.admin');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include authentication routes (login, register, password reset)
require __DIR__ . '/auth.php';
