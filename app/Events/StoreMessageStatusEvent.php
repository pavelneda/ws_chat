<?php

namespace App\Events;

use App\Http\Resources\Message\MessageBroadcastResource;
use App\Http\Resources\Message\MessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreMessageStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $chat_id;
    private $user_id;
    private $count;


    /**
     * Create a new event instance.
     */
    public function __construct($chat_id, $user_id, $count)
    {


        $this->chat_id = $chat_id;
        $this->user_id = $user_id;
        $this->count = $count;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->user_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'store-message-status';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chat_id,
            'user_id' => $this->user_id,
            'count' => $this->count,
        ];
    }
}

