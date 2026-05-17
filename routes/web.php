<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Broadcast;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Chat private
    Route::get('/chat/{id?}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/message', [MessageController::class, 'store'])->name('message.store');

    // Grup
    Route::get('/groups',                   [GroupController::class, 'index'])->name('groups.index');
    Route::post('/groups',                  [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{id}',              [GroupController::class, 'show'])->name('groups.show');
    Route::post('/groups/{id}/message',     [GroupController::class, 'sendMessage'])->name('groups.message');
    Route::post('/groups/{id}/add-member',  [GroupController::class, 'addMember'])->name('groups.addMember');
    Route::delete('/groups/{id}',           [GroupController::class, 'destroy'])->name('groups.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Broadcast::routes(['middleware' => ['auth']]);

require __DIR__.'/auth.php';