<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreRequest;
use App\Http\Resources\Chat\ChatIndexResource;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\Message\MessageResource;
use App\Http\Resources\User\UserResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ChatController extends Controller
{

    public function index()
    {
        $users = User::anotherUsers();
        $users = UserResource::collection($users)->resolve();

        $chats = auth()->user()->chats()
            ->has('messages')
            ->with(['lastMessage', 'chatWith'])
            ->withCount('unreadableMessageStatus')
            ->get();

        $chats = ChatIndexResource::collection($chats)->resolve();

        return Inertia::render('Chat/Index', ['users' => $users, 'chats' => $chats]);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $userIds = array_merge($data['users'], [auth()->id()]);
        sort($userIds);
        $userIdsString = implode('-', $userIds);


        try {
            DB::beginTransaction();

            if (isset($data['is_group']) && $data['is_group']) {
                $chat = Chat::create(['title' => $data['title'], 'users' => $userIdsString]);
            } else {
                $chat = Chat::firstOrCreate(
                    ['users' => $userIdsString],
                    ['title' => $data['title'], 'users' => $userIdsString]
                );
            }

            $chat->users()->sync($userIds);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }

        return redirect()->route('chats.show', $chat->id);
    }

    public function show(Chat $chat)
    {
        $page = request('page', 1);

        $users = $chat->users()->get();
        $messages = $chat->getMessageWithPagination($page);

        $chat->readMessages();

        $isLastPage = $messages->onLastPage();
        $messages = MessageResource::collection($messages)->resolve();
        if ($page > 1) {
            return response()->json([
                'messages' => $messages,
                'is_last_page' => $isLastPage
            ]);
        }

        $chat = ChatResource::make($chat)->resolve();
        $users = UserResource::collection($users)->resolve();

        return Inertia::render('Chat/Show', compact('chat', 'users', 'messages', 'isLastPage'));
    }
}
