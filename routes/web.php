<?php

use App\Livewire\KontaktSplitter;
use App\Livewire\SavedInputs;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('splitter');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('splitter', KontaktSplitter::class)->name('splitter');
    Route::get('saved', SavedInputs::class)->name('saved');
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
