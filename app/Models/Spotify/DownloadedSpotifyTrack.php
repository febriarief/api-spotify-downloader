<?php

namespace App\Models\Spotify;
use Illuminate\Database\Eloquent\Model;

class DownloadedSpotifyTrack extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'track_id',
        'url'
    ];
}
