from modules.respot import Respot, RespotUtils
from pathlib import Path
from getpass import getpass
from modules.utils import Archive
from modules.tagger import AudioTagger
import argparse, json, os


CONFIG_DIR   = f"{os.path.dirname(__file__)}\configs"
TEMP_DIR     = f"{os.path.dirname(__file__)}\temp"
DOWNLOAD_DIR = f"{os.path.dirname(__file__)}\..\..\..\storage\app\public\downloads"

ANTI_BAN_WAIT_TIME = 5
ANTI_BAN_WAIT_TIME_ALBUMS = 30
LIMIT_RESULTS = 10


class Spotify:
    def __init__(self):
        self.SEPARATORS = [",", ";"]
        self.args = self.parse_args()
        self.audio_format = "mp3"

        self.respot = Respot(
            config_dir=CONFIG_DIR,
            force_premium=False,
            credentials=Path(CONFIG_DIR) / "credentials.json",
            audio_format=self.audio_format,
            antiban_wait_time=ANTI_BAN_WAIT_TIME,
        )

        self.search_limit = LIMIT_RESULTS

        self.config_dir = Path(CONFIG_DIR)
        self.download_dir = Path(TEMP_DIR)
        self.music_dir = Path(DOWNLOAD_DIR)

        self.album_in_filename = False
        self.antiban_album_time = ANTI_BAN_WAIT_TIME_ALBUMS
        self.not_skip_existing = True
        self.skip_downloaded = False
        self.archive_file = self.config_dir / "archive.json"
        self.archive = Archive(self.archive_file)
        self.tagger = AudioTagger()

    def parse_args(self):
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
        return parser.parse_args()
    
    def login(self):
        while not self.respot.is_authenticated():
            username = input("Username: ")
            password = getpass("Password: ")
            if self.respot.is_authenticated(username, password):
                return True
        return True
    
    def download_by_url(self, url):
        parsed_url = RespotUtils.parse_url(url)
        if parsed_url["track"]:
            ret = self.download_track(parsed_url["track"])
        else:
            return { "status": "download-error", "message": "Invalid provided url." }
        return ret
        
    def download_track(self, track_id, path=None, caller=None):
        track = self.respot.request.get_track_info(track_id)

        if track is None:
            return { "status": "download-error", "message": "Track not found." }

        if not track["is_playable"]:
            return { "status": "download-error", "message": "Track is not playable." }

        audio_name   = track.get("audio_name")
        audio_number = track.get("audio_number")
        artist_name  = track.get("artist_name")
        album_artist = track.get("album_artist")
        album_name   = track.get("album_name")

        filename = self.generate_filename(
            caller,
            audio_name,
            audio_number,
            artist_name,
            album_name,
        )

        base_path = path or self.music_dir
        if caller == "show" or caller == "episode":
            base_path = path or self.episodes_dir

        temp_path = base_path / (filename + "." + self.audio_format)

        output_path = self.respot.download(
            track_id, temp_path, self.audio_format, True
        )

        self.archive.add(
            track_id,
            artist=artist_name,
            track_name=audio_name,
            fullpath=output_path,
            audio_type="music",
        )

        self.tagger.set_audio_tags(
            output_path,
            artists=artist_name,
            name=audio_name,
            album_name=album_name,
            release_year=track["release_year"],
            disc_number=track["disc_number"],
            track_number=audio_number,
            album_artist=album_artist,
            track_id_str=track["scraped_song_id"],
            image_url=track["image_url"],
        )

        return {"status": "download-success", "message": "Download success", "data": {"path": str(output_path)}} 

    def generate_filename(
            self,
            caller,
            audio_name,
            audio_number,
            artist_name,
            album_name,
        ):
            if caller == "album":
                filename = f"{audio_number}. {audio_name}"

                if self.album_in_filename:
                    filename = f"{album_name} " + filename

            elif caller == "playlist":
                filename = f"{audio_name}"

                if self.album_in_filename:
                    filename = f"{album_name} - " + filename
                filename = f"{artist_name} - " + filename

            elif caller == "show":
                filename = f"{audio_number}. {audio_name}"

            elif caller == "episode":
                filename = f"{artist_name} - {audio_number}. {audio_name}"

            else:
                filename = f"{artist_name} - {audio_name}"

            filename = self.shorten_filename(filename, artist_name, audio_name)
            filename = RespotUtils.sanitize_data(filename)

            return filename
            
    @staticmethod
    def shorten_filename(filename, artist_name, audio_name, max_length=50):
        if len(filename) > max_length and len(artist_name) > (max_length // 2):
            filename = filename.replace(artist_name, "Various Artists")
        else:
            excess_length = len(filename) - max_length
            truncated_audio_name = audio_name[:-excess_length]
            filename = filename.replace(audio_name, truncated_audio_name)

        return filename
    
    def login(self):
        """Login to Spotify"""
        while not self.respot.is_authenticated():
            print("Login to Spotify")
            username = input("Username: ")
            password = getpass("Password: ")
            if self.respot.is_authenticated(username, password):
                return True
        return True

    def start(self):
        if self.respot.is_authenticated() == False:
            print(json.dumps({"status": "error", "message": "Unauthenticated", "data": ""}))
            return
        
        paths_to_check = (
            self.config_dir,
            self.download_dir,
            self.music_dir
        )

        try:
            self.archive.archive_migration(paths_to_check)
            print(json.dumps(self.download_by_url(self.args.track)))
        except Exception as e:
            print(json.dumps({"status": "download-error", "message": str(e), "data": ""}))

    def get_info(self):
        if self.respot.is_authenticated() == False:
            print(json.dumps({"status": "error", "message": "Unauthenticated", "data": "[]"}))
            return

        paths_to_check = (
            self.config_dir,
            self.download_dir,
            self.music_dir,
        )

        self.archive.archive_migration(paths_to_check)

        url = self.args.info
        parsed_url = RespotUtils.parse_url(url)
        if parsed_url["track"]:
            track_info = self.respot.request.get_track_info(parsed_url["track"])
            if track_info is None:
                print(json.dumps({"status": "error", "message": "Cannot get track info", "data": "[]"}))
                return
            else:
                print(json.dumps({"status": "success", "data": track_info, "message": ""}))
                return
        else:
            print(json.dumps({"status": "error", "message": "Invalid url", "data": "[]"}))
            return

    def delete_track(self):
        filename = self.args.delete
        filepath = self.music_dir / filename
        if os.path.isfile(filepath) and os.path.exists(filepath):
            os.remove(filepath)

        print(json.dumps({"status": "success-delete", "message": "Success delete file {filename}", "data": "[]"}))
