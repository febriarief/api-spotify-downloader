<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Spotify\SpotifyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('spotify/get-queue', [SpotifyController::class, 'getQueue']);
Route::post('spotify/get-info', [SpotifyController::class, 'getInfo']);
Route::post('spotify/request-download', [SpotifyController::class, 'requestDownload']);
Route::post('spotify/process-download', [SpotifyController::class, 'processDownload']);
