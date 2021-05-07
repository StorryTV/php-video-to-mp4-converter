#!/bin/bash

currentdir=$(PWD)
sudo apt install ffmpeg -y
sudo chmod -R 775 $currentdir/original $currentdir/converted
sudo crontab -l | { cat; echo "0       *       *       *       *       $currentdir/cleanup.sh"; } | crontab -
