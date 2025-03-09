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
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
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
Route::prefix('collectes')->middleware(['auth'])->group(function () {
    Route::post('/{collecte}/join', [CollecteController::class, 'join'])->name('collecte.join');
});

Route::get('/signal/thank-you', [SignalController::class, 'thankYou'])->name('signal.thank-you');

    // Profile Management Routes
    /*Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });*/

    // Password Change Routes
    Route::get('/password/change', [ChangePasswordController::class, 'edit'])->name('password.change');
    Route::post('/password/change', [ChangePasswordController::class, 'update']);

    // Under Review Notification
// Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Article routes
Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/featured', [ArticleController::class, 'featured'])->name('articles.featured');
    Route::get('/category/{category}', [ArticleController::class, 'byCategory'])->name('articles.category');
    Route::get('/tag/{slug}', [ArticleController::class, 'byTag'])->name('articles.tag');
    Route::get('/{id}', [ArticleController::class, 'show'])->name('articles.show');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/signals', [SignalManagementController::class, 'index'])->name('signals.index');
    Route::get('/signals/{signal}', [SignalManagementController::class, 'show'])->name('signals.show');
    Route::get('/signals/{signal}/edit', [SignalManagementController::class, 'edit'])->name('signals.edit');
    Route::put('/signals/{signal}', [SignalManagementController::class, 'update'])->name('signals.update');
    Route::delete('/signals/{signal}', [SignalManagementController::class, 'destroy'])->name('signals.destroy');
    Route::post('/signals/{signal}/status', [SignalManagementController::class, 'updateStatus'])->name('signals.update-status');
    Route::get('/signals/export', [SignalManagementController::class, 'export'])->name('signals.export');
    Route::get('/signals/statistics', [SignalManagementController::class, 'getStatistics'])->name('signals.statistics');
});
