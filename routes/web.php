<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\LiveBookingController;
use App\Http\Controllers\Admin\ReportController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Auth::routes();

// Admin Routes - Protected by auth and role middleware
Route::middleware(['auth', 'role:owner,admin,staff'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Bookings
    Route::resource('bookings', BookingController::class);
    
    // Live Booking - Realtime
    Route::get('/live-booking', [LiveBookingController::class, 'index'])->name('live-booking.index');
    Route::get('/live-booking/data', [LiveBookingController::class, 'getData'])->name('live-booking.data');
    Route::patch('/live-booking/{booking}/status', [LiveBookingController::class, 'updateStatus'])->name('live-booking.update-status');
    
    // Reports - Only Owner and Admin
    Route::middleware('role:owner,admin')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/booking', [ReportController::class, 'bookingReport'])->name('reports.booking');
        Route::get('/reports/revenue', [ReportController::class, 'revenueReport'])->name('reports.revenue');
        Route::get('/reports/booking/pdf', [ReportController::class, 'exportBookingPdf'])->name('reports.booking.pdf');
        Route::get('/reports/booking/excel', [ReportController::class, 'exportBookingExcel'])->name('reports.booking.excel');
    });
    
    // POS - Point of Sale
    Route::get('/pos', [\App\Http\Controllers\Admin\POSController::class, 'index'])->name('pos.index');
    Route::post('/pos', [\App\Http\Controllers\Admin\POSController::class, 'store'])->name('pos.store');
    Route::get('/pos/sales', [\App\Http\Controllers\Admin\POSController::class, 'sales'])->name('pos.sales');
    Route::get('/pos/{sale}', [\App\Http\Controllers\Admin\POSController::class, 'show'])->name('pos.show');
    Route::get('/pos/print/{sale}', [\App\Http\Controllers\Admin\POSController::class, 'print'])->name('pos.print');
    
    // Branch Management - Only Owner and Admin
    Route::middleware('role:owner,admin')->group(function () {
        Route::resource('branches', \App\Http\Controllers\Admin\BranchController::class);
        Route::resource('fields', \App\Http\Controllers\Admin\FieldController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('transactions', \App\Http\Controllers\Admin\TransactionController::class);
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    });
    
    // Settings - Only Owner and Admin
    Route::middleware('role:owner,admin')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    });
});

// API Routes for AJAX
Route::middleware(['auth', 'role:owner,admin,staff'])->prefix('api')->name('api.')->group(function () {
    Route::get('/fields/by-branch/{branch}', function($branchId) {
        return \App\Models\Field::where('branch_id', $branchId)->active()->get();
    })->name('fields.by-branch');
    
    Route::get('/field-availability/{field}', function(\App\Models\Field $field, \Illuminate\Http\Request $request) {
        $available = $field->isAvailable(
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->exclude_booking_id
        );
        
        return response()->json(['available' => $available]);
    })->name('field.availability');
    
    Route::get('/current-time', function() {
        $timezone = \App\Models\Setting::get('app_timezone', 'Asia/Jakarta');
        $time = now()->setTimezone($timezone)->format('H:i:s');
        return response()->json(['time' => $time]);
    })->name('current-time');
});

// Redirect authenticated users to admin dashboard
Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');
