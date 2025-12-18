<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TherapistController;
use App\Http\Controllers\AvailabilitySlotController;
use App\Http\Controllers\TherapySessionController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\VideoCallController;
use App\Http\Controllers\Api\PaddleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/contact', function () {
    return Inertia::render('Contact', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('contact');

// Locale switcher
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Debug route
Route::get('/test-translations', function () {
    \Log::info('Session locale: ' . session('locale', 'not set'));
    \Log::info('App locale: ' . app()->getLocale());
    return Inertia::render('Test', [
        'sessionLocale' => session('locale', 'not set'),
        'appLocale' => app()->getLocale(),
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Therapists
    Route::get('/therapists', [TherapistController::class, 'index'])->name('therapists.index');
    Route::get('/therapists/{therapist}', [TherapistController::class, 'show'])->name('therapists.show');
    Route::put('/therapist/profile', [TherapistController::class, 'updateProfile'])->name('therapist.profile.update');

    // Availability Slots
    Route::prefix('availability')->name('availability.')->group(function () {
        Route::get('/', [AvailabilitySlotController::class, 'index'])->name('index');
        Route::post('/', [AvailabilitySlotController::class, 'store'])->name('store');
        Route::post('/bulk', [AvailabilitySlotController::class, 'bulkCreate'])->name('bulk');
        Route::delete('/{slot}', [AvailabilitySlotController::class, 'destroy'])->name('destroy');
    });

    // Therapy Sessions
    Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/', [TherapySessionController::class, 'index'])->name('index');
        Route::post('/', [TherapySessionController::class, 'store'])->name('store');
        Route::get('/{session}', [TherapySessionController::class, 'show'])->name('show');
        Route::put('/{session}/status', [TherapySessionController::class, 'updateStatus'])->name('update-status');
        Route::post('/{session}/confirm', [TherapySessionController::class, 'confirm'])->name('confirm');
        Route::post('/{session}/complete', [TherapySessionController::class, 'complete'])->name('complete');
        Route::post('/{session}/cancel', [TherapySessionController::class, 'cancel'])->name('cancel');
        Route::post('/{session}/notes', [TherapySessionController::class, 'saveNotes'])->name('notes');
        Route::get('/{session}/video', [TherapySessionController::class, 'joinVideo'])->name('video');
    });

    // Messages
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/conversation/{user}', [MessageController::class, 'conversation'])->name('conversation');
        Route::post('/', [MessageController::class, 'store'])->name('store');
        Route::get('/unread-count', [MessageController::class, 'unreadCount'])->name('unread-count');
    });

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/history', [PaymentController::class, 'history'])->name('history');
        Route::get('/{payment}/complete', [PaymentController::class, 'complete'])->name('complete');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/process', [PaymentController::class, 'process'])->name('process');
    });

    // Video Calls
    Route::prefix('video-call')->name('video.')->group(function () {
        Route::get('/', [VideoCallController::class, 'index'])->name('call');
        Route::post('/call-user', [VideoCallController::class, 'callUser'])->name('call.user');
    });

    // Paddle API routes (authenticated web routes)
    Route::prefix('api')->group(function () {
        Route::post('/create-paddle-price', [PaddleController::class, 'createPrice']);
        Route::post('/calculate-session-price', [PaddleController::class, 'calculatePrice']);
        Route::get('/session-duration-options', [PaddleController::class, 'getDurationOptions']);
    });
});

// Paddle webhook (no auth middleware)
Route::post('/api/paddle/webhook', [PaddleController::class, 'webhook']);
