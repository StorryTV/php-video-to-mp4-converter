# Converting video files to MP4

This simple script uploads a video file and converts it with **ffmpeg** to **mp4** format and then stores it on IPFS to download it from.

Cheers!


## Requirements


You need **php-ipfs-api** to get your converted mp4 video from ipfs.

```bash
composer require rannmann/php-ipfs-api
composer install
```


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

No installation besides the above needed. Just make sure you have the right file permission on the **original** and **converted** folders.

```bash
sudo chmod -R 775 original converted
```
