#!/bin/bash

currentdir=$(PWD)
sudo chmod -R 775 $currentdir/original $currentdir/converted
sudo crontab -l | { cat; echo "0       *       *       *       *       $currentdir/cleanup.sh"; } | crontab -
