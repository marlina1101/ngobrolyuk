<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender');
    }

    public function broadcastOn(): array
    {
        // Harus PresenceChannel (bukan PrivateChannel) karena
        // channels.php mendefinisikan 'chat' sebagai Broadcast::presence()
        return [
            new PresenceChannel('chat')
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}