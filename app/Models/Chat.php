<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class Chat extends Model
{
    protected $table = 'chats';
    protected $guarded = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_user', 'chat_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id', 'id');
    }

    public function unreadableMessageStatus()
    {
        return $this->hasMany(MessageStatus::class, 'chat_id', 'id')
            ->where('user_id', auth()->id())
            ->where('is_read', false);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'chat_id', 'id')
            ->latestOfMany();
    }

    public function chatWith()
    {
        return $this->hasOneThrough(User::class, ChatUser::class, 'chat_id', 'id', 'id', 'user_id')
            ->whereNot('user_id', auth()->id());
    }

    public function getMessageWithPagination($page) :LengthAwarePaginator
    {
        return $this->messages()
            ->orderByDesc('created_at')
            ->paginate(5, '*', 'page', $page);
    }

    public function readMessages()
    {
        return $this->unreadableMessageStatus()->update(['is_read' => true]);
    }
}
