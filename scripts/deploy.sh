#!/usr/bin/env bash
# Deploy SMS to production (Hostinger)
# Usage: ./scripts/deploy.sh [commit-message]
# Or: npm run deploy

set -e

SSH="ssh -p 65002 u820431346@145.14.146.15"
REMOTE_PATH="~/public_html/sms"

echo "=== 1. Building frontend (Vite) ==="
npm run build

echo ""
echo "=== 2. Git add & status ==="
git add -A
git status

if [[ -n $(git status --porcelain) ]]; then
  MSG="${1:-Deploy: $(date '+%Y-%m-%d %H:%M')}"
  echo ""
  echo "=== 3. Committing: $MSG ==="
  git commit -m "$MSG"
fi

echo ""
echo "=== 4. Pushing to origin ==="
git push

echo ""
echo "=== 5. Pulling on server & clearing config ==="
# Remote: set -e so a failed step aborts (avoids 'Deploy complete' when the site is half-updated).
# Laravel package manifests inside bootstrap/cache are generated files. If they were built
# from a dev install, production can fail trying to boot dev-only providers (for example Pail).
# We clear only those generated manifests right before rebuilding autoload/discovery.
$SSH "set -e
cd $REMOTE_PATH || exit 1
echo '--- git pull ---'
git pull
echo '--- composer install ---'
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
echo '--- clear generated bootstrap cache files ---'
rm -f bootstrap/cache/*.php
echo '--- composer dump-autoload ---'
composer dump-autoload --optimize --no-dev --no-interaction --no-scripts
echo '--- artisan package:discover ---'
php artisan package:discover --ansi
echo '--- artisan migrate ---'
php artisan migrate --force
echo '--- artisan optimize:clear ---'
php artisan optimize:clear
echo '--- artisan config:cache ---'
php artisan config:cache
echo '--- artisan route:cache ---'
php artisan route:cache
echo '--- artisan view:cache ---'
php artisan view:cache
echo '--- storage link (ignore if exists) ---'
php artisan storage:link 2>/dev/null || true
echo '--- remote deploy OK ---'
"

echo ""
echo "✅ Deploy complete."
