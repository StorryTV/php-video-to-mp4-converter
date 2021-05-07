#!/bin/bash

full_path=$(realpath $0)
currentdir=$(dirname $full_path)
sudo apt install ffmpeg -y
sudo chmod -R 775 $currentdir/original $currentdir/converted
sudo crontab -l | { cat; echo "0       *       *       *       *       $currentdir/cleanup.sh"; } | crontab -
echo "Installation done!"
