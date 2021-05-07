# Converting video files to MP4

This simple script uploads a video file and converts it with **ffmpeg** to **mp4** format.

Cheers!

## Automatic installation

You can follow this automatic script (make sure you are in the root directory of the website).
```bash
sudo chmod 754 install.sh
sudo install.sh
```

Or you can do it all manually using the rest of this readme below.


## Requirements


You need **ffmpeg** to be able to convert.

On Mac OS with Homebrew:

```bash
brew install ffmpeg
```

On Ubuntu:

```bash
sudo apt install ffmpeg
```


## Installation

No installation besides the above needed. Just make sure you have the right file permission on the **original** and **converted** folders.

```bash
sudo chmod -R 775 original converted
```

## Automatic cleanup

You can also make a cronjob to automatically delete uploaded and converted videos older than 1 day if you want.
```bash
sudo crontab -e
```

Paste the following at the end of the file (just make sure the path is where your app is located):
```bash
0 * * * * /var/www/html/cleanup.sh
```
