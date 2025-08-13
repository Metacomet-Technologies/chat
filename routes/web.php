<?php

use App\Http\Controllers\Web\ChatController;
use App\Http\Controllers\Web\RoomController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Rooms
    Route::get('rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('rooms/{room:slug}', [RoomController::class, 'show'])->name('rooms.show');

    // Chat (legacy)
    Route::get('chat', [ChatController::class, 'index'])->name('chat');
    Route::get('chat/{room}', [ChatController::class, 'show'])->name('chat.room');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
