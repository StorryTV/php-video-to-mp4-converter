currentdir=$(PWD)
/usr/bin/find $currentdir/original/ -name "*.mp4" -type f -mtime +1 -exec rm -f {} \;
/usr/bin/find $currentdir/converted/ -name "*.mp4" -type f -mtime +1 -exec rm -f {} \;
