<?php

namespace App\Jobs;

use App\Events\StoreMessageStatusEvent;
use App\Models\MessageStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreMessageStatusJob implements ShouldQueue
{
    use Queueable;

    private $userIds;
    private $data;
    private $message;

    /**
     * Create a new job instance.
     */
    public function __construct($userIds, $data, $message)
    {
        //
        $this->userIds = $userIds;
        $this->data = $data;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->userIds as $userId) {
            MessageStatus::create([
                'message_id' => $this->message->id,
                'user_id' => $userId,
                'chat_id' => $this->data['chat_id'],
            ]);

            $count = MessageStatus::where('user_id', $userId)
                ->where('chat_id', $this->data['chat_id'])
                ->where('is_read', false)->count();

            broadcast(new StoreMessageStatusEvent($this->data['chat_id'], $userId, $count, $this->message));
        }
    }
}
