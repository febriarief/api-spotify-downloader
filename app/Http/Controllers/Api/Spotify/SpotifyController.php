<?php

namespace App\Http\Controllers\Api\Spotify;

use App\Events\Spotify\SpotifyDownloadJobEvent;
use App\Http\Controllers\Controller;
use App\Services\Spotify\SpotifyTrackDownloaderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;

use App\Models\Spotify\DownloadedSpotifyTrack;

class SpotifyController extends Controller
{
    /**
     * Retrieve the size of the queue and return it as a JSON success response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQueue(Request $request)
    {
        $queue = Queue::size();
        return json_success_response(200, null, ['queue' => $queue]);
    }

    /**
     * Retrieve information about a track from Spotify based on the provided URL.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInfo(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['url' => 'required'], ['url.required' => 'Please provide a valid url.']);
        if ($validator->stopOnFirstFailure()->fails()) return json_error_response(422, $validator->errors()->first());

        $spotifyService = new SpotifyTrackDownloaderService();
        $output = $spotifyService->getTrackInfo($input['url']);

        if ($output->status == 'error') return json_error_response(422, $output->message);
        return json_success_response(200, null, $output->data);
    }

    /**
     * Handle a request to download a track, check if it exists in the database,
     * and provide information about its status (existence, queue, or readiness).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestDownload(Request $request)
    {
        $trackId = $request->get('track_id', null);
        $socketId = $request->get('socket_id', null);
        if (!$trackId || !$socketId) return json_error_response(422, 'Missing param id track or socket.');

        $availableTrack = DownloadedSpotifyTrack::where('track_id', $trackId)->first();
        if ($availableTrack) {
            return json_success_response(200, null, ['status' => 'exist', 'url' => $availableTrack->url]);
        }

        $queue = Queue::size();
        if ($queue > 0) {
            $this->processDownload($request);
            return json_success_response(200, null, ['status' => 'queue', 'queue' => $queue]);
        } else {
            return json_success_response(200, null, ['status' => 'ready']);
        }
    }

    /**
     * Initiate the process to download a track by dispatching a SpotifyDownloadJobEvent.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processDownload(Request $request)
    {
        $trackId  = $request->get('track_id', null);
        $socketId = $request->get('socket_id', null);
        if (!$trackId || !$socketId) return json_error_response(422, 'Missing param id track or socket.');
        event(new SpotifyDownloadJobEvent($socketId, $trackId));
        return json_success_response(200, 'processing download');
    }
}
