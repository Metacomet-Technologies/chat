<?php

use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('v1')->name('api.v1.')->group(function () {
    // Room management
    Route::apiResource('rooms', \App\Http\Controllers\Api\V1\Rooms\RoomController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::post('rooms/join', [\App\Http\Controllers\Api\V1\Rooms\RoomController::class, 'join'])->name('rooms.join');
    Route::delete('rooms/{room}/leave', [\App\Http\Controllers\Api\V1\Rooms\RoomController::class, 'leave'])->name('rooms.leave');
    Route::get('rooms/{room}/members', [\App\Http\Controllers\Api\V1\Rooms\RoomController::class, 'members'])->name('rooms.members');

    // Messages
    Route::apiResource('rooms.messages', MessageController::class)->only(['index', 'store']);
});
