<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Domains\Messages\Models\Room;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('chat', [
            'selectedRoomId' => null,
        ]);
    }

    public function show(Request $request, Room $room): Response
    {
        // Check if user has access to this room
        if (! $room->hasUser($request->user())) {
            abort(403, 'You do not have access to this room');
        }

        return Inertia::render('chat', [
            'selectedRoomId' => $room->id,
        ]);
    }
}
