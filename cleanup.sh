/usr/bin/find ./original/ -name "*.mp4" -type f -mtime +1 -exec rm -f {} \;
/usr/bin/find ./converted/ -name "*.mp4" -type f -mtime +1 -exec rm -f {} \;
