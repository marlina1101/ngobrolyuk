<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message =
            $message->load('sender');
    }

    public function broadcastOn(): array
    {
        return [

            // pengirim
            new PrivateChannel(
                'chat.' .
                $this->message->sender_id
            ),

            // penerima
            new PrivateChannel(
                'chat.' .
                $this->message->receiver_id
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'sender_id' =>
                    $this->message->sender_id,

                'receiver_id' =>
                    $this->message->receiver_id,

                'message' =>
                    $this->message->message,

                'file' =>
                    $this->message->file,

                'created_at' =>
                    $this->message->created_at,

                'sender' =>
                    $this->message->sender,
            ]
        ];
    }
}