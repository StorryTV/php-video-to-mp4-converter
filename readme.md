# Converting video files to MP4

This simple script uploads a video file and converts it with **ffmpeg** to **mp4** format.

Cheers!


## Requirements


You need **ffmpeg** to be able to convert.

On Mac OS with Homebrew:

```bash
brew install ffmpeg
```

On Ubuntu (as root):

```bash
apt install ffmpeg
```
On Ubuntu (as another user using 'sudo'):

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
0 0 * * * /usr/bin/find /var/www/html/original/ -name "*.mp4" -type f -mtime +1 -exec rm -f {} \;
0 0 * * * /usr/bin/find /var/www/html/converted/ -name "*.mp4" -type f -mtime +1 -exec rm -f {} \;
```
