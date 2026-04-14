<?php

declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/sheets.php';

requireAuth();
startSecureSession();

$username = $_SESSION['username'] ?? 'Investigador';
$userId = (int) $_SESSION['user_id'];
$sheets = [];

try {
  $sheets = listSheetsByUser($userId);
} catch (\Throwable $e) {
  $_SESSION['error'] = 'Nao foi possivel carregar suas fichas agora. Verifique a configuracao do banco.';
}

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Painel - Call of Cthulhu</title>
  <link rel="stylesheet" href="/style/main-style.css">
</head>

<body>
  <div class="app-shell">
    <header class="site-topbar">
      <div>
        <h1 class="brand-title">Arquivo do Investigador</h1>
        <p class="brand-sub">Painel de Investigadores</p>
      </div>
      <div class="news-actions">
        <a class="btn" href="/sheet_create.php">Nova ficha</a>
        <a class="btn btn-outline" href="/php/logout.php">Sair</a>
      </div>
    </header>

    <main class="page">
      <section class="news-shell card">
        <div class="hero">
          <h2 class="headline">Personagens em Arquivo</h2>
          <p>Investigador ativo: <strong><?php echo htmlspecialchars((string) $username); ?></strong></p>
        </div>

        <?php if ($success): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars((string) $success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars((string) $error); ?></div>
        <?php endif; ?>

        <?php if ($sheets === []): ?>
          <p class="muted">Nenhuma ficha criada ainda. Clique em "Nova ficha" para comecar.</p>
        <?php else: ?>
          <div class="sheet-grid">
            <?php foreach ($sheets as $sheet): ?>
              <article class="sheet-card">
                <h3><?php echo htmlspecialchars((string) $sheet['character_name']); ?></h3>
                <p class="sheet-meta">Ocupacao: <?php echo htmlspecialchars((string) ($sheet['occupation'] ?? '')); ?></p>
                <p class="sheet-meta">PV: <?php echo (int) ($sheet['hp'] ?? 0); ?> / <?php echo (int) ($sheet['max_hp'] ?? 0); ?></p>
                <p class="sheet-meta">SAN: <?php echo (int) ($sheet['sanity'] ?? 0); ?> / <?php echo (int) ($sheet['max_sanity'] ?? 0); ?></p>
                <p class="sheet-meta">Atualizada: <?php echo htmlspecialchars((string) $sheet['updated_at']); ?></p>
                <div class="news-actions">
                  <a class="btn" href="/sheet_view.php?id=<?php echo (int) $sheet['id']; ?>">Abrir e editar</a>
                  <a class="btn btn-outline" href="/sheet_delete.php?id=<?php echo (int) $sheet['id']; ?>">Excluir</a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>

</html>