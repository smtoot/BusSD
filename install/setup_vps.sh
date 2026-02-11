#!/bin/bash

# TransLab VPS Setup Script for Ubuntu 22.04 (Virtualmin Friendly)
# Usage: sudo bash setup_vps.sh

set -e # Exit on error

echo "Started TransLab VPS Setup..."

# 1. Add PHP 8.3 Repository
echo ">>> Adding PHP 8.3 PPA..."
sudo apt-get update
sudo apt-get install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update

# 2. Install PHP 8.3 & Extensions (Skipping libapache2-mod-php8.3 for Virtualmin)
echo ">>> Installing PHP 8.3..."
sudo apt-get install -y php8.3 php8.3-cli php8.3-common php8.3-fpm php8.3-mysql php8.3-zip php8.3-gd php8.3-mbstring php8.3-curl php8.3-xml php8.3-bcmath php8.3-intl php8.3-soap php8.3-imagick php8.3-cgi

# 3. Install Composer
echo ">>> Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    echo "Composer installed successfully."
else
    echo "Composer is already installed."
fi

# 4. Install Node.js 20.x
echo ">>> Installing Node.js 20..."
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
    sudo apt-get install -y nodejs
    echo "Node.js installed successfully."
else
    echo "Node.js is already installed."
fi

# 5. Verification
echo ">>> Verifying Installations..."
php8.3 -v
composer -V
node -v

echo "----------------------------------------------------------------"
echo "✅ Setup Complete!"
echo "Please go to Virtualmin > Server Configuration > PHP Options"
echo "and ensure the execution mode is set to FPM or CGI for your domain."
echo "----------------------------------------------------------------"
