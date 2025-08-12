<?php

use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('v1')->name('api.')->group(function () {
    Route::apiResource('rooms', RoomController::class)->only(['index', 'store', 'show']);
    Route::post('rooms/{room}/join', [RoomController::class, 'join'])->name('rooms.join');

    Route::apiResource('rooms.messages', MessageController::class)->only(['index', 'store']);
});
