<img src="https://res.cloudinary.com/idevart/image/upload/v1701009610/images/wguilqxxbiya9fjtibtr.png" width="200" alt="Spotify Downloader Logo">

<p align="center">
<img src="https://res.cloudinary.com/idevart/image/upload/v1701012749/images/vcxwab3d5intbygulfyy.png" width="800" alt="Spotify Downloader Web App">
</p>


## About Spotify Downloader API

This repository not only houses a robust API for Spotify track downloading but also incorporates a Python script for seamless integration with the [ZSpotify's](https://github.com/jsavargas/zspotify) library. This script allows users to effortlessly download Spotify tracks, and it has been enhanced with additional code to meet the specific integration requirements of our API.

## Features
- Real-Time Updates: Experience real-time updates on track download progress, enhancing the user's sense of control and feedback.
- Spotify Integration: Connect effortlessly to the Spotify platform, enabling users to specify tracks for download.
- Laravel Framework: Benefit from the robust features and structure provided by the Laravel framework for a secure and scalable API.
- Python Processing: Enhance the capabilities of the application with Python code, ensuring efficient and reliable handling of Spotify track downloads.

## Requirements
- PHP version >= 8
- Python version >= 3.10

## Pre-installation
- <b>Angular Web UI</b><br/>
You can find my angular web ui project [here](https://github.com/febriarief/console-spotify-downloader)
- <b>Cloudinary account</b><br/>
This application uses Cloudinary as a storage services. You can support me by registering for an account using my referral: [Sign up Cloudinary](https://console.cloudinary.com/invites/lpov9zyyucivvxsnalc5/vghmeghvl43raitzixwb?t=default)
- <b>Spotify account</b><br/>
<b>Free Account</b> will download 160k bitrate of track.<br/>
<b>Premium Account</b> will download 320k bitrate of track.<br/>
Recommend using a burner account to avoid any possible account bans.

## Installation
1. Clone the repository to your local environment. 
```bash
git clone https://github.com/febriarief/api-spotify-downloader.git
```
2. Install laravel packages
```bash
composer install
```
3. Copy file `.env.example` to `.env`.
4. Since we are using the `beyondcode/laravel-websockets` package, it is recommended to change the values of `PUSHER_APP_ID` and `PUSHER_APP_KEY` to `spotify-track-downloader` from the `.env` file. Documentation of [beyondcode/laravel-websockets](https://beyondco.de/docs/laravel-websockets/getting-started/installation)
```console
PUSHER_APP_ID=spotify-track-downloader
PUSHER_APP_KEY=spotify-track-downloader
```
5. Modify several keys in the .env file below to match the values in your Cloudinary account. 
```bash
CLOUDINARY_API_KEY=
CLOUDINARY_API_SECRET=
CLOUDINARY_URL=
CLOUDINARY_UPLOAD_PRESET=
```
6. Migrate database
```bash
php artisan migrate
```
7. Create python virtual environment
```bash
python -m venv venv
```
8. Activate python virtual environment & install module dependencies.
```console
Windows:
venv\Script\activate && pip install -r requirements.txt

Linux:
source venv\bin\activate && pip install -r requirements.txt
```
9. Change `QUEUE_CONNECTION` value from the `.env` file with `redis` or `database`
```bash
QUEUE_CONNECTION=redis

or 

QUEUE_CONNECTION=database
```
10. Run laravel websocket
```bash
php artisan websockets:serve
```
11. Run laravel queue process
```bash
php artisan queue:work
```
12. Build angular project and place it to `public` folder and name it `console`.

## After Installation
1. Open terminal and point to `app\Python\spotify`.
2. Login to your spotify account. Run command:
```bash
Windows:
venv\Script\activate && python main.py -l true

Linux:
source venv\bin\activate && python main.py -l true
```
<img src="https://res.cloudinary.com/idevart/image/upload/v1701014875/images/ig20dx8i5azvub48k4jy.png" width="500" alt="Spotify Downloader Web App">
3. Input your email/username and password.

## Python Script for Spotify Track Download
To complement the API, a Python script leveraging [ZSpotify](https://github.com/jsavargas/zspotify) has been included. This script facilitates the download of Spotify tracks effortlessly. Note that this script is an extension of the original [ZSpotify](https://github.com/jsavargas/zspotify) functionality, specifically tailored to seamlessly integrate with our API.

## Disclaimer
This application is not an official product of Spotify. Users are advised to use it at their own risk. Respect copyright and licensing agreements when downloading and using Spotify tracks.

## Contribution
Feel free to contribute to the development of this Spotify Track Downloader API. Fork the repository, make your changes, and submit a pull request.

## Thanks
Thanks to [jsavargas](https://github.com/jsavargas) for creating [ZSpotify](https://github.com/jsavargas/zspotify).

## License
This application is also licensed under the [MIT License](https://opensource.org/licenses/MIT), promoting an open and collaborative development environment.
