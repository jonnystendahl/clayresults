<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TrainingResultController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::redirect('/dashboard', '/results')->name('dashboard');

    Route::get('/results', [TrainingResultController::class, 'index'])->name('training-results.index');
    Route::post('/results', [TrainingResultController::class, 'store'])->name('training-results.store');
    Route::get('/results/{trainingResult}/edit', [TrainingResultController::class, 'edit'])->name('training-results.edit');
    Route::put('/results/{trainingResult}', [TrainingResultController::class, 'update'])->name('training-results.update');
    Route::delete('/results/{trainingResult}', [TrainingResultController::class, 'destroy'])->name('training-results.destroy');

    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function (): void {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
