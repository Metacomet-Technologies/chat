<?php

declare(strict_types=1);

namespace App\Events;

use App\Domains\Messages\Data\V1\MessageResponse;
use App\Domains\Messages\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public MessageResponse $message;

    public function __construct(Message $message)
    {
        // Ensure user is loaded before converting to response
        $message->loadMissing('user');
        $this->message = MessageResponse::fromModel($message)->include('user');
    }

    /**
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('room.' . $this->message->roomId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
