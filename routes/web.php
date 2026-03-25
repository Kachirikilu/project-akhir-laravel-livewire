<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsDosen;
use App\Http\Middleware\IsStaff;
use App\Http\Middleware\IsMahasiswa;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::middleware(['is_admin'])->group(function () {
        Route::view('user-management', 'user-management')->name('user-management');
        Route::view('user-lite', 'user-lite')->name('user-lite');
        Route::view('program-studi-management', 'program-studi-management')->name('program-studi-management');
    });

    Route::middleware(['is_staff'])->group(function () {
        Route::view('mata-kuliah-management', 'mata-kuliah-management')->name('mata-kuliah-management');
        Route::view('rps-management', 'rps-management')->name('rps-management');
    });

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
