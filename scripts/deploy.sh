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
# Do NOT delete bootstrap/cache manifests before package:discover — if SSH dies mid-way, Laravel 500s until discover runs.
$SSH "set -e
cd $REMOTE_PATH || exit 1
echo '--- git pull ---'
git pull
echo '--- composer install ---'
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
echo '--- composer dump-autoload ---'
composer dump-autoload --optimize --no-dev --no-interaction
echo '--- artisan package:discover ---'
php artisan package:discover --ansi
echo '--- artisan migrate ---'
php artisan migrate --force
echo '--- artisan optimize:clear ---'
php artisan optimize:clear
echo '--- storage link (ignore if exists) ---'
php artisan storage:link 2>/dev/null || true
echo '--- remote deploy OK ---'
"

echo ""
echo "✅ Deploy complete."
