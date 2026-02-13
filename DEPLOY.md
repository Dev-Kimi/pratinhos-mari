# Deploy deste projeto

## Erro na Vercel: `404: NOT_FOUND`

Esse repositório é uma aplicação PHP (`.php`) e o código está na pasta `pratinhos/`.
Na Vercel, esse formato não funciona de forma nativa para backend PHP tradicional, então é comum cair em `NOT_FOUND`.

## Erro na Railway: `Railpack could not determine how to build the app`

Esse erro aconteceu porque o Railpack analisou a raiz do repositório e só encontrou uma subpasta (`pratinhos/`) sem um entrypoint padrão na raiz.

### Correção aplicada neste repositório

Foi adicionado um script `start.sh` na raiz para iniciar o PHP embutido apontando para a pasta correta:

```bash
#!/usr/bin/env bash
set -euo pipefail
PORT="${PORT:-8080}"
cd pratinhos
exec php -S 0.0.0.0:${PORT} -t .
```

Com isso, a Railway consegue detectar e iniciar o app.

## Como subir na Railway

1. Conecte o repositório normalmente.
2. Garanta que o deploy está usando a **raiz do repositório** (onde está o `start.sh`).
3. Faça novo deploy.
4. Abra a URL gerada.

## Observações

- Esse modo usa o servidor embutido do PHP (bom para deploy simples).
- Se quiser ambiente mais robusto, depois podemos migrar para Docker + Nginx/Apache.
