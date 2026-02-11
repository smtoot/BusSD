#!/bin/bash
# Usage: sudo bash finish_deploy.sh <username>

TARGET_USER=$1

if [ -z "$TARGET_USER" ]; then
    echo "Error: Please specify the username."
    echo "Usage: sudo bash finish_deploy.sh <username>"
    exit 1
fi

TARGET_DIR="/home/$TARGET_USER/public_html"

if [ ! -d "$TARGET_DIR" ]; then
    echo "Error: Directory $TARGET_DIR does not exist."
    exit 1
fi

echo "👉 Moving files to $TARGET_DIR..."

# Enable dotglob to match hidden files like .env
shopt -s dotglob

# Move files from upload dir to public_html
cp -r /home/ubuntu/translab_upload/* $TARGET_DIR/

echo "👉 Setting file permissions..."
# Set ownership to the Virtualmin user
chown -R $TARGET_USER:$TARGET_USER $TARGET_DIR

# Set directory permissions
find $TARGET_DIR -type d -exec chmod 755 {} \;
# Set file permissions
find $TARGET_DIR -type f -exec chmod 644 {} \;

# Set write permissions for storage and cache
chmod -R 777 $TARGET_DIR/core/storage
chmod -R 777 $TARGET_DIR/core/bootstrap/cache

echo "✅ Files deployed and permissions fixed!"
