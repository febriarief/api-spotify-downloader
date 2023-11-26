<?php

namespace App\Events\Spotify;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpotifyDownloaderEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public mixed  $data;

    private string $event;
    private string $socketId;

    /**
     * Create a new event instance.
     */
    public function __construct($event, $socketId, $data)
    {
        $this->event    = $event;
        $this->socketId = $socketId;
        $this->data     = $data;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return $this->event;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('channel.spotify-track-downloader.' . $this->socketId);
    }
}
