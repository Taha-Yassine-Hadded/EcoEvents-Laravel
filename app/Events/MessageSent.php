<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public ChatMessage $message)
    {
        // Ensure user relation is loaded for payload
        $this->message->loadMissing('user:id,name');
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.room.' . $this->message->chat_room_id);
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'message_type' => $this->message->message_type ?? 'text',
            'voice_url' => $this->message->voice_url,
            'user' => [
                'id' => $this->message->user->id,
                'name' => $this->message->user->name,
                'profile_image' => $this->message->user->profile_image ?? null,
            ],
            'created_at' => $this->message->created_at?->toIso8601String(),
            'room_id' => $this->message->chat_room_id,
        ];
    }
}
