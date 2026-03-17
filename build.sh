#!/bin/bash

# current_dir=/home/jjones/projects/robinhood
current_dir=/home/tech/projects/dvr
echo "Current directory: $current_dir"
# cd $current_dir
# git pull
git -C $current_dir pull

sudo docker compose -f $current_dir/prod-docker-compose.yml up  --build --force-recreate -d