<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\SignalController;
use App\Http\Controllers\HomeController;
use Database\Seeders\RoleSeeder;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CollecteController;
use App\Http\Controllers\Admin\SignalManagementController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\ProfileController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home and Overview Routes
Route::get('/', [OverviewController::class, 'index'])->name('home');
Route::get('/overview', [OverviewController::class, 'index'])->name('overview');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registration Routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Password Reset Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'show'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->name('verification.verify')
        ->middleware(['signed', 'throttle:30,1']);

    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->name('verification.resend')
        ->middleware('throttle:3,1');
});

// Dashboard Route - Protected by auth and verified middleware
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
});

// Admin Routes
/*Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    // Users Management
    Route::resource('users', UsersController::class);
    // Approve Supervisor Accounts
    Route::post('/users/{user}/approve', [UsersController::class, 'approve'])->name('admin.users.approve');
});*/
/*Route::get('/debug-session', function () {
    return response()->json(session()->all());
})->middleware('web');
Route::get('/test-auth', function () {
    return response()->json([
        'user' => Auth::user(),
        'session' => session()->all(),
    ]);
})->middleware(['web', 'auth']);*/

Route::prefix('signal')->group(function () {
    // Show the form to create a new signal
    Route::get('/create', [SignalController::class, 'create'])->name('signal.create');
    Route::post('/store', [SignalController::class, 'store'])->name('signal.store');
    Route::get('/', [SignalController::class, 'index'])->name('signal.index');
});

// Collecte routes
Route::prefix('collectes')->middleware(['auth', 'verified'])->group(function () {
    // List all collectes
    Route::get('/', [CollecteController::class, 'index'])->name('collecte.index');
    
    // Create new collecte
    Route::get('/create', [CollecteController::class, 'create'])->name('collecte.create');
    Route::post('/', [CollecteController::class, 'store'])->name('collecte.store');
    
    // Specific collecte actions
    Route::get('/{collecte}/edit', [CollecteController::class, 'edit'])->name('collecte.edit');
    Route::put('/{collecte}', [CollecteController::class, 'update'])->name('collecte.update');
    Route::delete('/{collecte}', [CollecteController::class, 'destroy'])->name('collecte.destroy');
    Route::patch('/{collecte}/status', [CollecteController::class, 'updateStatus'])->name('collecte.update-status');
    Route::post('/{collecte}/join', [CollecteController::class, 'join'])->name('collecte.join');
    Route::post('/{collecte}/leave', [CollecteController::class, 'leave'])->name('collecte.leave');
    
    // Completion and report routes
    Route::post('/{collecte}/complete', [CollecteController::class, 'complete'])->name('collecte.complete');
    Route::get('/{collecte}/report', [CollecteController::class, 'downloadReport'])->name('collecte.report.download');
    
    // Show collecte (keep this last to avoid catching other routes)
    Route::get('/{collecte}', [CollecteController::class, 'show'])->name('collecte.show');
});

Route::get('/signal/thank-you', [SignalController::class, 'thankYou'])->name('signal.thank-you');



    // Password Change Routes
    Route::get('/password/change', [ChangePasswordController::class, 'edit'])->name('password.change');
    Route::post('/password/change', [ChangePasswordController::class, 'update']);

    // Under Review Notification
// Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Public article routes
Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/featured', [ArticleController::class, 'featured'])->name('articles.featured');
    Route::get('/category/{category}', [ArticleController::class, 'byCategory'])->name('articles.category');
    Route::get('/tag/{slug}', [ArticleController::class, 'byTag'])->name('articles.tag');
    Route::get('/{article}', [ArticleController::class, 'show'])->name('articles.show');
});

// Admin article routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Article Routes
    Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class);
    
    // Signal Management Routes
    Route::prefix('signals')->name('signals.')->group(function () {
        // List and basic operations
        Route::get('/', [SignalManagementController::class, 'index'])->name('index');
        
        // Batch validation routes - define these before resource routes
        Route::get('/batch-validate', [SignalManagementController::class, 'showBatchValidate'])->name('batch-validate');
        Route::post('/batch-validate', [SignalManagementController::class, 'batchValidate'])->name('batch-validate.store');
        
        // Other signal management routes
        Route::get('/anomalies', [SignalManagementController::class, 'anomalies'])->name('anomalies');
        Route::get('/export/{format}', [SignalManagementController::class, 'export'])->name('export');
        Route::post('/{signal}/status', [SignalManagementController::class, 'updateStatus'])->name('update-status');
        
        // Resource routes - keep these last
        Route::get('/{signal}', [SignalManagementController::class, 'show'])->name('show');
        Route::get('/{signal}/edit', [SignalManagementController::class, 'edit'])->name('edit');
        Route::put('/{signal}', [SignalManagementController::class, 'update'])->name('update');
        Route::delete('/{signal}', [SignalManagementController::class, 'destroy'])->name('destroy');
    });
});
