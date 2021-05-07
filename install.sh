#!/bin/bash

full_path=$(realpath $0)
currentdir=$(dirname $full_path)
sudo apt install ffmpeg -y
sudo chmod -R 775 $currentdir/original $currentdir/converted
sudo chmod 750 $currentdir/cleanup.sh
sudo crontab -l | { cat; echo "0       *       *       *       *       $currentdir/cleanup.sh"; } | crontab -
echo "Installation done!"
