#!/usr/bin/env bash
# Run this ON THE SERVER (after SSH) to pull latest code.
# Usage: bash server-pull.sh   or   curl -sL <url> | bash

cd ~/public_html/sms || exit 1
git fetch origin
git pull origin main
php artisan config:clear
echo "Done."
