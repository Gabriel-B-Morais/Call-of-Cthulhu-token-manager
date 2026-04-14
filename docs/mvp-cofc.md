# MVP - Gerenciador de Fichas Call of Cthulhu (InfinityFree)

## Objetivo

Criar um sistema simples para cadastrar, editar e visualizar fichas de personagem de Call of Cthulhu usando PHP + MySQL em hospedagem gratis (InfinityFree), sem coleta de dados sensiveis como e-mail, nome real, telefone ou endereco.

## Escopo inicial (MVP)

- Cadastro de conta com `username` + senha (sem e-mail).
- Login e logout.
- CRUD de fichas (criar, listar, editar, excluir).
- Campos principais da ficha:
  - Identificacao: nome do personagem, ocupacao, idade, residencia, local de nascimento.
  - Atributos: FOR, CON, DES, APA, POD, INT, TAM, EDU.
  - Derivados: PV, SAN, PM, MOV, Bonus de Dano, Build.
  - Pericias principais (valor base + ajustes do jogador).
  - Equipamentos e anotacoes livres.
- Dono da ficha: cada usuario so acessa as proprias fichas.

## Fora do escopo (por enquanto)

- Recuperacao de senha por e-mail.
- Upload de arquivos/imagens.
- Compartilhamento publico de fichas.
- Integracao com APIs externas.

## Stack recomendada

- Backend: PHP 8+ (procedural simples ou MVC leve).
- Banco: MySQL/MariaDB (fornecido pelo InfinityFree).
- Frontend: HTML + CSS + JS basico.
- Sessao: `$_SESSION`.
- Senhas: `password_hash` e `password_verify`.

## Estrutura sugerida

- `index.php` -> home/login.
- `register.php` -> cadastro de usuario.
- `dashboard.php` -> lista de fichas.
- `sheet_create.php` -> criar ficha.
- `sheet_edit.php?id=` -> editar ficha.
- `sheet_view.php?id=` -> visualizar ficha.
- `sheet_delete.php?id=` -> exclusao com confirmacao.
- `auth.php` -> funcoes de autenticacao e guardas.
- `db.php` -> conexao PDO.
- `config.php` -> configuracoes nao sensiveis.

## Modelo de dados (MySQL)

### Tabela `users`

- `id` INT PK AUTO_INCREMENT
- `username` VARCHAR(30) UNIQUE NOT NULL
- `password_hash` VARCHAR(255) NOT NULL
- `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP

### Tabela `character_sheets`

- `id` INT PK AUTO_INCREMENT
- `user_id` INT NOT NULL (FK logica para users.id)
- `character_name` VARCHAR(80) NOT NULL
- `occupation` VARCHAR(80) NULL
- `age` TINYINT UNSIGNED NULL
- `residence` VARCHAR(120) NULL
- `birthplace` VARCHAR(120) NULL
- `str_val` TINYINT UNSIGNED NOT NULL
- `con_val` TINYINT UNSIGNED NOT NULL
- `dex_val` TINYINT UNSIGNED NOT NULL
- `app_val` TINYINT UNSIGNED NOT NULL
- `pow_val` TINYINT UNSIGNED NOT NULL
- `int_val` TINYINT UNSIGNED NOT NULL
- `siz_val` TINYINT UNSIGNED NOT NULL
- `edu_val` TINYINT UNSIGNED NOT NULL
- `hp` TINYINT UNSIGNED NULL
- `sanity` TINYINT UNSIGNED NULL
- `magic_points` TINYINT UNSIGNED NULL
- `move_rate` TINYINT UNSIGNED NULL
- `damage_bonus` VARCHAR(20) NULL
- `build` VARCHAR(20) NULL
- `skills_json` TEXT NULL
- `equipment_text` TEXT NULL
- `notes_text` TEXT NULL
- `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

## Regras de seguranca essenciais

- Usar PDO com prepared statements em todas as queries.
- Validar autenticacao em todas as paginas protegidas.
- Sempre filtrar por `user_id` nas operacoes de leitura/edicao/exclusao.
- Regenerar sessao apos login: `session_regenerate_id(true)`.
- Escapar saida HTML com `htmlspecialchars`.
- Nao expor erros SQL em producao.

## Privacidade (sem dados sensiveis)

- Coletar apenas:
  - `username` (pode ser apelido)
  - senha
  - dados da ficha ficticia
- Nao coletar e-mail, nome civil, telefone, documento, endereco real.
- Incluir pagina curta de privacidade explicando os dados armazenados.

## Fluxo do usuario

1. Usuario cria conta com username e senha.
2. Faz login.
3. Entra no dashboard.
4. Cria uma ou mais fichas.
5. Edita ou exclui fichas proprias.
6. Faz logout.

## Roadmap rapido

1. Implementar autenticacao minima.
2. Implementar CRUD de ficha com verificacao por `user_id`.
3. Melhorar UX do formulario (mascaras, validacoes, calculos).
4. Adicionar calculo automatico de campos derivados.
5. Adicionar exportacao simples para PDF (opcional).

## Pronto para codar

Prioridade de desenvolvimento:

1. `db.php` e migracao SQL.
2. `register.php` e `login` em `index.php`.
3. `dashboard.php` com listagem.
4. `sheet_create.php` + `sheet_edit.php`.
5. Protecoes de seguranca e validacoes finais.
