#!/bin/bash

# Change directory to your Laravel project directory
cd /var/www/r1riepas

# Delete session files in the storage directory
find storage/framework/sessions -type f -delete

echo "Session files deleted successfully."
