#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-8080}"

if ! command -v php >/dev/null 2>&1; then
  echo "Erro: comando 'php' não encontrado no ambiente atual." >&2
  echo "Na Railway, prefira deploy via Dockerfile para garantir runtime PHP." >&2
  exit 1
fi

cd pratinhos
exec php -S 0.0.0.0:${PORT} -t .
