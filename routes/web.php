<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\ArticleController as UserArticleController;
use App\Http\Controllers\User\SimulationController;
use App\Http\Controllers\User\CommentController as UserCommentController;

/*
|--------------------------------------------------------------------------
| USER ROUTES (Public)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('user.home');
Route::get('/artikel', [UserArticleController::class, 'index'])->name('user.articles.index');
Route::get('/artikel/{slug}', [UserArticleController::class, 'show'])->name('user.articles.show');
Route::get('/simulasi', [SimulationController::class, 'index'])->name('user.simulation');
Route::post('/simulasi/hitung', [SimulationController::class, 'calculate'])->name('user.simulation.calculate');

// Native Comment (with CheckBanned middleware)
Route::post('/artikel/{slug}/komentar', [UserCommentController::class, 'store'])
    ->middleware('check.banned')
    ->name('user.comments.store');

    Route::get('/artikel/{slug}/komentar', [UserCommentController::class, 'index'])
    ->name('user.comments.index');

/*
|--------------------------------------------------------------------------
| ADMIN AUTH ROUTES (Guest only)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // /admin → redirect ke login atau dashboard
    Route::get('/', function () {
        return auth()->check()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('admin.login');
    });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/lupa-password', [AuthController::class, 'showForgot'])->name('forgot');
    Route::post('/lupa-password', [AuthController::class, 'sendResetCode']);
    Route::get('/verifikasi', [AuthController::class, 'showVerify'])->name('verify');
    Route::post('/verifikasi', [AuthController::class, 'verifyCode']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('reset-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (Authenticated)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Articles CRUD
        Route::get('/artikel', [AdminArticleController::class, 'index'])->name('articles.index');
        Route::get('/artikel/tambah', [AdminArticleController::class, 'create'])->name('articles.create');
        Route::post('/artikel', [AdminArticleController::class, 'store'])->name('articles.store');
        Route::get('/artikel/{article}/sunting', [AdminArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/artikel/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
        Route::delete('/artikel/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
        Route::get('/artikel/{slug}/preview', [AdminArticleController::class, 'preview'])->name('articles.preview');

        // Comments Moderation
        Route::get('/komentar', [AdminCommentController::class, 'index'])->name('comments.index');
        Route::post('/komentar/{id}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
        Route::post('/komentar/{id}/reject', [AdminCommentController::class, 'reject'])->name('comments.reject');
        Route::post('/komentar/{id}/device-ban', [AdminCommentController::class, 'deviceBan'])->name('comments.device-ban');
        Route::post('/komentar/{id}/undevice-ban', [AdminCommentController::class, 'undeviceBan'])->name('comments.undevice-ban');
        Route::post('/komentar/{id}/reply', [AdminCommentController::class, 'reply'])->name('comments.reply');
        Route::get('/komentar/export', [AdminCommentController::class, 'exportLog'])->name('comments.export');

        // Profile
        Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profil/password', [ProfileController::class, 'changePassword'])->name('profile.password');
        Route::post('/profil/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
        Route::delete('/profil/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
        Route::post('/profil/email/request', [ProfileController::class, 'requestEmailChange'])->name('profile.email.request');
        Route::post('/profil/email/verify-otp', [ProfileController::class, 'verifyEmailOtp'])->name('profile.email.verify-otp');
        Route::get('/profil/email/verify/{token}', [ProfileController::class, 'verifyEmail'])->name('profile.email.verify');

        // Logs
        Route::get('/logs', [LogController::class, 'index'])->name('logs');
        Route::get('/logs/export', [LogController::class, 'export'])->name('logs.export');
    });
});