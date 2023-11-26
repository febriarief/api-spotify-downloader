from spotify_downloader import Spotify
import argparse

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument(
        "-l", "--login", help="Login to spotify account"
    )
    parser.add_argument(
        "-tr", "--track", help="Downloads a track from their id or url"
    )
    parser.add_argument(
        "-i", "--info", help="Url containing track"
    )
    parser.add_argument(
        "-d", "--delete", help="Delete existing track"
    )

    args = parser.parse_args()
    
    spotify = Spotify()
    if args.login:
        spotify.login()
    elif args.track:
        spotify.start()
    elif args.info:
        spotify.get_info()
    elif args.delete:
        spotify.delete_track()

