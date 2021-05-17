#!/bin/bash

full_path=$(realpath $0)
currentdir=$(dirname $full_path)
sudo apt install ffmpeg -y
sudo chmod -R 775 $currentdir/original $currentdir/converted
sudo chmod 770 $currentdir/cleanup.php
sudo chmod 750 $currentdir/install.sh
sudo chmod 750 $currentdir/log.txt
echo "Installation done!"
