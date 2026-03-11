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
$SSH "cd $REMOTE_PATH && git pull && php artisan config:clear && php artisan storage:link 2>/dev/null || true"

echo ""
echo "✅ Deploy complete."
