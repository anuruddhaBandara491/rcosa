<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MonthlyPaymentController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\MemberReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('registration-payments', RegistrationPaymentController::class);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Members — full resource CRUD
    // Routes generated:
    //  GET     /members              → index   (members.index)
    //  GET     /members/create       → create  (members.create)
    //  POST    /members              → store   (members.store)
    //  GET     /members/{member}     → show    (members.show)
    //  GET     /members/{member}/edit → edit   (members.edit)
    //  PUT     /members/{member}     → update  (members.update)
    //  DELETE  /members/{member}     → destroy (members.destroy)
    Route::resource('members', MemberController::class);

    Route::get('monthly-payments/search-member', [MonthlyPaymentController::class, 'searchMember'])->name('monthly-payments.search-member');
    Route::get('monthly-payments/member-summary/{member}', [MonthlyPaymentController::class, 'memberSummary'])->name('monthly-payments.member-summary');
    Route::resource('monthly-payments', MonthlyPaymentController::class);

    Route::get('donations/search-member', [DonationController::class, 'searchMember'])->name('donations.search-member');
    Route::get('donations/member-total/{member}', [DonationController::class, 'memberTotal'])->name('donations.member-total');
    Route::resource('donations', DonationController::class);

    Route::get('member-reports/search', [MemberReportController::class, 'search'])->name('reports.search');
    Route::get('member-reports', [MemberReportController::class, 'index'])->name('reports.index');

});

require __DIR__.'/auth.php';
