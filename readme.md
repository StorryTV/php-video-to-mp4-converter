# Converting video files to MP4

This simple script uploads a video file and converts it with **ffmpeg** to **mp4** format.

Note that converting to webm is painfully slow! Just give it some time and make a nice progress bar...

Cheers!


## Requirements

You need **ffmpeg** to be able to convert.

On Mac OS with Homebrew:

```bash
brew install ffmpeg
```

On Ubuntu:

```bash
apt install ffmpeg
```


## Installation

No installation besides ffmpeg needed. Just make sure you have the right file permission on the **original** and **converted** folders.

```bash
sudo chmod -R 775 original converted
```
