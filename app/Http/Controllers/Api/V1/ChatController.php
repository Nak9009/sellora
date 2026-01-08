<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Models\{Chat, Message, Ad};
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $chats = Chat::where('user_id', auth()->id())
            ->orWhere('seller_id', auth()->id())
            ->with('ad', 'user', 'seller', 'messages')
            ->latest('last_message_at')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => ChatResource::collection($chats),
            'meta' => ['total' => $chats->total()]
        ]);
    }

    public function show(Chat $chat)
    {
        if (auth()->id() !== $chat->user_id && auth()->id() !== $chat->seller_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $chat->load('ad', 'user', 'seller');

        return response()->json([
            'success' => true,
            'data' => new ChatResource($chat)
        ]);
    }

    public function startChat(Request $request, Ad $ad)
    {
        if ($ad->user_id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot chat with own ad'
            ], 422);
        }

        $chat = Chat::firstOrCreate(
            [
                'ad_id' => $ad->id,
                'user_id' => auth()->id(),
                'seller_id' => $ad->user_id,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => new ChatResource($chat->load('ad', 'user', 'seller'))
        ], 201);
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        if (auth()->id() !== $chat->user_id && auth()->id() !== $chat->seller_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate(['message' => 'required|string|max:5000']);

        $receiver_id = auth()->id() === $chat->user_id ? $chat->seller_id : $chat->user_id;

        $message = $chat->messages()->create([
            'sender_id' => auth()->id(),
            'receiver_id' => $receiver_id,
            'message' => $request->message,
        ]);

        // Update chat's last message
        $chat->update([
            'last_message' => $request->message,
            'last_message_at' => now(),
        ]);

        // Send notification (implement later)
        // Notification::send($chat->otherUser(), new NewMessageNotification($message));

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => new MessageResource($message)
        ], 201);
    }

    public function messages(Request $request, Chat $chat)
    {
        if (auth()->id() !== $chat->user_id && auth()->id() !== $chat->seller_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $chat->messages()
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => MessageResource::collection($messages)
        ]);
    }

    public function markAsRead(Chat $chat, Message $message)
    {
        if (auth()->id() !== $message->receiver_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read',
            'data' => new MessageResource($message)
        ]);
    }

    public function destroy(Chat $chat)
    {
        if (auth()->id() !== $chat->user_id && auth()->id() !== $chat->seller_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $chat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chat deleted successfully'
        ]);
    }
}
