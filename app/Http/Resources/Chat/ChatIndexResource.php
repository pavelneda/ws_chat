<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\Message\MessageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? 'Chat with ' . $this->chatWith->name,
            'users' => $this->users,
            'unreadable_message_status_count' => $this->unreadable_message_status_count,
            'last_message' => isset($this->lastMessage) ? MessageResource::make($this->lastMessage)->resolve() : null,
        ];
    }
}
