<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ClubManagementController;
use App\Http\Controllers\Admin\ClubMembershipController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClubMemberPasswordController;
use App\Http\Controllers\ClubModuleController;
use App\Http\Controllers\ClubSelectionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicClubController;
use App\Http\Controllers\TrainingResultController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/clubs/{club}', [PublicClubController::class, 'show'])->name('clubs.show');
Route::get('/clubs/{club}/news', [ClubModuleController::class, 'news'])->name('clubs.news');
Route::get('/clubs/{club}/events', [ClubModuleController::class, 'events'])->name('clubs.events');
Route::get('/clubs/{club}/board', [ClubModuleController::class, 'board'])->name('clubs.board');
Route::get('/clubs/{club}/membership-renewal', [ClubModuleController::class, 'renewal'])->name('clubs.renewal');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::redirect('/dashboard', '/')->name('dashboard');

    Route::get('/email/verify', function (Request $request): View|RedirectResponse {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request): RedirectResponse {
        $request->fulfill();

        return redirect()->route('home')
            ->with('status', 'Your email address has been verified.');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request): RedirectResponse {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        $request->user()->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')
            ->with('status', 'A new verification link has been sent to your email address.');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::post('/clubs/{club}/main', [ClubSelectionController::class, 'update'])->name('clubs.main.update');
    Route::post('/clubs/{club}/members/{user}/temporary-password', [ClubMemberPasswordController::class, 'store'])->name('clubs.members.password.store');
    Route::post('/clubs/{club}/membership-renewal', [ClubModuleController::class, 'storeRenewalRequest'])->name('clubs.renewal.store');

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

        Route::get('/clubs/{club}/news', [\App\Http\Controllers\Admin\ClubNewsPostController::class, 'index'])->name('clubs.news.index');
        Route::post('/clubs/{club}/news', [\App\Http\Controllers\Admin\ClubNewsPostController::class, 'store'])->name('clubs.news.store');
        Route::put('/clubs/{club}/news/{newsPost}', [\App\Http\Controllers\Admin\ClubNewsPostController::class, 'update'])->name('clubs.news.update');
        Route::delete('/clubs/{club}/news/{newsPost}', [\App\Http\Controllers\Admin\ClubNewsPostController::class, 'destroy'])->name('clubs.news.destroy');

        Route::get('/clubs/{club}/events', [\App\Http\Controllers\Admin\ClubEventController::class, 'index'])->name('clubs.events.index');
        Route::post('/clubs/{club}/events', [\App\Http\Controllers\Admin\ClubEventController::class, 'store'])->name('clubs.events.store');
        Route::put('/clubs/{club}/events/{event}', [\App\Http\Controllers\Admin\ClubEventController::class, 'update'])->name('clubs.events.update');
        Route::delete('/clubs/{club}/events/{event}', [\App\Http\Controllers\Admin\ClubEventController::class, 'destroy'])->name('clubs.events.destroy');

        Route::get('/clubs/{club}/board', [\App\Http\Controllers\Admin\ClubBoardMemberController::class, 'index'])->name('clubs.board.index');
        Route::post('/clubs/{club}/board', [\App\Http\Controllers\Admin\ClubBoardMemberController::class, 'store'])->name('clubs.board.store');
        Route::put('/clubs/{club}/board/{boardMember}', [\App\Http\Controllers\Admin\ClubBoardMemberController::class, 'update'])->name('clubs.board.update');
        Route::delete('/clubs/{club}/board/{boardMember}', [\App\Http\Controllers\Admin\ClubBoardMemberController::class, 'destroy'])->name('clubs.board.destroy');

        Route::get('/clubs/{club}/renewal', [\App\Http\Controllers\Admin\ClubRenewalController::class, 'edit'])->name('clubs.renewal.edit');
        Route::put('/clubs/{club}/renewal', [\App\Http\Controllers\Admin\ClubRenewalController::class, 'update'])->name('clubs.renewal.update');
        Route::put('/clubs/{club}/renewal/{renewalRequest}', [\App\Http\Controllers\Admin\ClubRenewalController::class, 'updateRequest'])->name('clubs.renewal.requests.update');
    });
});
