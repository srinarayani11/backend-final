<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Fetch messages between two users
    public function fetchMessages($receiverId)
    {
        $userId = Auth::id();
        $messages = Message::conversation($userId, $receiverId)
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

    // Send a new message
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required_without:file|nullable|string',
            'file' => 'nullable|file|max:20480', // Max 20MB
        ]);

        $data = [
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
            'message_type' => 'text',
            'is_read' => false,
        ];

        // File upload (optional)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('chat_files', 'public');

            $data['file_url'] = '/storage/' . $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['message_type'] = 'file';
        }

        $message = Message::create($data);

        // Broadcast event
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }
}
