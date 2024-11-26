<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageStatus\UpdateRequest;
use App\Models\MessageStatus;
use Illuminate\Http\Request;

class MessageStatusController extends Controller
{
    public function update(UpdateRequest $request)
    {
        $data = $request->validated();

        $messageStatus = MessageStatus::where('chat_id', $data['chat_id'])
            ->where('user_id', $data['user_id'])
            ->update(['is_read' => true]);
    }
}
