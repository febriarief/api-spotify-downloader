<?php

namespace App\Listeners\Spotify;

use App\Events\Spotify\SpotifyDownloaderEvent;
use App\Events\Spotify\SpotifyDownloaderQueueEvent;
use App\Events\Spotify\SpotifyDownloadJobEvent;
use App\Models\Spotify\DownloadedSpotifyTrack;
use App\Services\Cloudinary\CloudinaryService;
use App\Services\Spotify\SpotifyTrackDownloaderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Queue;

class SpotifyDownloadJobListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
          
    }

    /**
     * Handle the event.
     */
    public function handle(SpotifyDownloadJobEvent $event): void
    {
        event(new SpotifyDownloaderQueueEvent(Queue::size()));
        
        $availableTrack = DownloadedSpotifyTrack::where('track_id', $event->trackId)->first();
        if ($availableTrack) {
            event(new SpotifyDownloaderEvent('download-success', $event->socketId, ['path' => $availableTrack->url]));
        } else {
            event(new SpotifyDownloaderEvent('download-sleep', $event->socketId, []));
            sleep(10);
            
            event(new SpotifyDownloaderEvent('begin-download', $event->socketId, []));
            
            $spotifyService = new SpotifyTrackDownloaderService();
            $output = $spotifyService->processDownload($event->trackId, $event->socketId);
    
            if (isset($output->status) && $output->status == 'downloading-error') {
                event(new SpotifyDownloaderEvent('download-error', $event->socketId, ['message' => $output->message]));
            }
    
            if (isset($output->status) && $output->status == 'download-success') {
                $pathInfo = pathinfo($output->data->path);
                $filename = $pathInfo['basename'];
                $filepath = CloudinaryService::upload($output->data->path, $filename, 'spotify/downloads');
                
                DownloadedSpotifyTrack::create([
                    'track_id' => $event->trackId,
                    'url'      => $filepath
                ]);
    
                
                $spotifyService->deleteFile($filename);
                
                event(new SpotifyDownloaderEvent('download-success', $event->socketId, ['path' => $filepath]));
            }
        }

        $remainingQueue = Queue::size() > 0 ? Queue::size() - 1 : Queue::size();
        event(new SpotifyDownloaderQueueEvent($remainingQueue));
    }
}
