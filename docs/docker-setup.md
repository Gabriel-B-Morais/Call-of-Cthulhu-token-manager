# Docker Setup (Desenvolvimento Local)

Este projeto usa Docker apenas para desenvolvimento local.
Na InfinityFree (producao), voce sobe os arquivos PHP e configura o banco pelo painel deles.

## Requisitos

- Docker Desktop instalado.
- Porta `80` livre para app PHP.
- Porta `8081` livre para phpMyAdmin.
- Porta `3307` livre para acesso local ao MySQL.

## Arquivos de ambiente

1. Copie `.env.example` para `.env`.
2. Ajuste as senhas se quiser.

Exemplo PowerShell:

```powershell
Copy-Item .env.example .env
```

## Subir ambiente

```powershell
docker compose up -d --build
```

Servicos:

- App PHP: `http://localhost`
- phpMyAdmin: `http://localhost:8081`
  - Host: `db`
  - Usuario: `root`
  - Senha: valor de `DB_ROOT_PASSWORD` no `.env`

## Banco e schema

- O arquivo `docs/schema.sql` roda automaticamente no primeiro start do container `db`.
- Se voce mudar o schema e quiser recriar do zero:

```powershell
docker compose down -v
docker compose up -d --build
```

## Troubleshooting de login de teste

Se o login com `testuser` / `testpassword` falhar em um banco ja existente, aplique a correcao abaixo (o schema antigo tinha hash invalido):

```sql
SOURCE /var/www/html/docs/fix-testuser-login.sql;
```

Ou execute diretamente no phpMyAdmin:

```sql
UPDATE users
SET password_hash = '$2y$10$fw6JK6qtuRU6wxsaiCewJeCt9csQ8C2g6Msfv/QPAH2kDzyeURmoy'
WHERE username = 'testuser';
```

## Comandos uteis

```powershell
# Ver logs
docker compose logs -f

# Parar tudo
docker compose down

# Entrar no container PHP
docker compose exec app bash
```

## Observacao sobre InfinityFree

InfinityFree nao executa Docker no deploy final. Use este setup para desenvolver e testar localmente, depois publique os arquivos PHP e configure o banco remoto no painel da hospedagem.
