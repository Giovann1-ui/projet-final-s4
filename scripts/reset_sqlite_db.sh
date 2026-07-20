#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
DB_PATH="$ROOT_DIR/writable/database.sqlite"
LEGACY_DB_PATH="$ROOT_DIR/conge"
LEGACY_WRITABLE_DB_PATH="$ROOT_DIR/writable/conge"

mkdir -p "$(dirname "$DB_PATH")"
rm -f "$DB_PATH" "$LEGACY_DB_PATH" "$LEGACY_WRITABLE_DB_PATH"
touch "$DB_PATH"

cd "$ROOT_DIR"

php spark migrate
php spark db:seed DatabaseSeeder
