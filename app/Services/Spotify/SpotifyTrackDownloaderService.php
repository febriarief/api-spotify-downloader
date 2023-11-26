<?php

namespace App\Services\Spotify;

use App\Services\Python\PythonService;

class SpotifyTrackDownloaderService extends PythonService
{
    public function getTrackInfo($url)
    {
        $output = $this->run('D:\project-apps\python\spotify-downloader\main.py', [
            '-i ' . $url,
        ]);

        return json_decode($output); 
    }

    public function processDownload(string $trackId)
    {
        $output = $this->run('D:\project-apps\python\spotify-downloader\main.py', [
            '-tr https://open.spotify.com/track/' . $trackId,
        ]);

        return json_decode($output);
    }

    public function deleteFile(string $filename)
    {
        $output = $this->run('D:\project-apps\python\spotify-downloader\main.py', [
            '-d ' . '"' . $filename . '"',
        ]);

        return json_decode($output);
    }
}
