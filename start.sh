#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-8080}"

cd pratinhos
exec php -S 0.0.0.0:${PORT} -t .
