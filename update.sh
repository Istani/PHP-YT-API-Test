#!/usr/bin/sh
cd ~/SimpleYTHelper/
pm2 resurrect
git checkout master
git pull
pm2 restart 0
pm2 status