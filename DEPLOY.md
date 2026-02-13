# Deploy deste projeto

## Erro na Vercel: `404: NOT_FOUND`

Esse repositório é uma aplicação PHP (`.php`) e o código está na pasta `pratinhos/`.
Na Vercel, esse formato não funciona de forma nativa para backend PHP tradicional, então é comum cair em `NOT_FOUND`.

## Erro na Railway: `start.sh: line 7: exec: php: not found`

Esse erro indica que o container/ambiente iniciado pela Railway **não tinha binário `php` disponível** no runtime detectado.

## Correção aplicada (robusta)

Foi adicionado um `Dockerfile` na raiz usando imagem oficial `php:8.2-cli`, garantindo que o PHP exista no runtime:

```dockerfile
FROM php:8.2-cli
WORKDIR /app
COPY . /app
ENV PORT=8080
EXPOSE 8080
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t /app/pratinhos"]
```

Também foi mantido o `start.sh` para uso local/alternativo, com mensagem clara caso `php` não exista no ambiente.

## Como subir na Railway (recomendado)

1. Conecte o repositório normalmente.
2. Garanta que o serviço está usando o **Dockerfile da raiz**.
3. Faça novo deploy.
4. Abra a URL gerada.

## Observações

- Com Dockerfile, você não depende da detecção automática do Railpack para achar PHP.
- Se quiser, depois dá para evoluir para imagem com Nginx/Apache + PHP-FPM.
