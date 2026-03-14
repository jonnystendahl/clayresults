<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ClubManagementController;
use App\Http\Controllers\Admin\ClubMembershipController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClubSelectionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrainingResultController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::redirect('/dashboard', '/')->name('dashboard');

    Route::post('/clubs/{club}/main', [ClubSelectionController::class, 'update'])->name('clubs.main.update');

    Route::get('/results', [TrainingResultController::class, 'index'])->name('training-results.index');
    Route::post('/results', [TrainingResultController::class, 'store'])->name('training-results.store');
    Route::get('/results/{trainingResult}/edit', [TrainingResultController::class, 'edit'])->name('training-results.edit');
    Route::put('/results/{trainingResult}', [TrainingResultController::class, 'update'])->name('training-results.update');
    Route::delete('/results/{trainingResult}', [TrainingResultController::class, 'destroy'])->name('training-results.destroy');

    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function (): void {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');

        Route::get('/clubs', [ClubManagementController::class, 'index'])->name('clubs.index');
        Route::get('/clubs/create', [ClubManagementController::class, 'create'])->name('clubs.create');
        Route::post('/clubs', [ClubManagementController::class, 'store'])->name('clubs.store');
        Route::get('/clubs/{club}/edit', [ClubManagementController::class, 'edit'])->name('clubs.edit');
        Route::put('/clubs/{club}', [ClubManagementController::class, 'update'])->name('clubs.update');
        Route::delete('/clubs/{club}', [ClubManagementController::class, 'destroy'])->name('clubs.destroy');

        Route::post('/clubs/{club}/memberships', [ClubMembershipController::class, 'store'])->name('clubs.memberships.store');
        Route::put('/clubs/{club}/memberships/{clubMembership}', [ClubMembershipController::class, 'update'])->name('clubs.memberships.update');
        Route::delete('/clubs/{club}/memberships/{clubMembership}', [ClubMembershipController::class, 'destroy'])->name('clubs.memberships.destroy');
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
