<?php

namespace App\Http\Controllers;

use App\Events\StoreMessageEvent;
use App\Events\StoreMessageStatusEvent;
use App\Http\Requests\Message\MessageRequest;
use App\Http\Resources\Message\MessageResource;
use App\Models\Message;
use App\Models\MessageStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mailer\Event\MessageEvent;

class MessageController extends Controller
{
    public function store(MessageRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $userIds = $data['user_ids'];
        unset($data['user_ids']);

        try {
            DB::beginTransaction();
            $message = Message::create($data);

            foreach ($userIds as $userId) {
                MessageStatus::create([
                    'message_id' => $message->id,
                    'user_id' => $userId,
                    'chat_id' => $data['chat_id'],
                ]);

                $count = MessageStatus::where('user_id', $userId)
                    ->where('chat_id', $data['chat_id'])
                    ->where('is_read', false)->count();

                broadcast(new StoreMessageStatusEvent($data['chat_id'], $userId, $count));
            }

            broadcast(new StoreMessageEvent($message))->toOthers();
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(['errors' => $exception->getMessage()], 400);
        }

        return MessageResource::make($message)->resolve();
    }
}
