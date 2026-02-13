# Deploy deste projeto

## Por que deu `404: NOT_FOUND` na Vercel

Este repositório é um site **PHP** (arquivos `index.php`, `checkout.php`, `api/*.php`).
A Vercel não executa PHP nativamente para esse tipo de projeto, então o deploy acaba em rota não encontrada (`NOT_FOUND`) ou sem renderizar a aplicação.

Além disso, os arquivos do site estão dentro da pasta `pratinhos/`, e não na raiz do repositório.

## Opções recomendadas

### 1) Hospedar em provedor com PHP nativo (mais simples)
Use plataformas como:
- Hostinger / cPanel
- InfinityFree
- Railway (com container)
- Render (com container)

### 2) Se quiser continuar na Vercel
Será necessário migrar o backend PHP para outro stack (ex.: Node/Serverless) e manter apenas frontend estático na Vercel.

## Estrutura atual

- Frontend/Páginas: `pratinhos/*.php`
- APIs: `pratinhos/api/*.php`
- Admin: `pratinhos/admin/*.php`

