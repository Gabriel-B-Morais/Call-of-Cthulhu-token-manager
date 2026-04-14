<?php

declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/csrf.php';
require_once __DIR__ . '/php/sheets.php';

requireAuth();
startSecureSession();

$userId = (int) $_SESSION['user_id'];
$sheetId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sheetId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$sheetId || $sheetId < 1) {
  http_response_code(400);
  echo 'Ficha invalida.';
  exit;
}

$sheet = findSheetByIdAndUser($sheetId, $userId);
if ($sheet === null) {
  http_response_code(404);
  echo 'Ficha nao encontrada.';
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!validateCSRF($_POST['csrf'] ?? '')) {
    $_SESSION['error'] = 'Token invalido.';
    header('Location: /sheet_view.php?id=' . $sheetId);
    exit;
  }

  deleteSheet($sheetId, $userId);
  $_SESSION['success'] = 'Ficha excluida com sucesso.';
  header('Location: /dashboard.php');
  exit;
}

$csrf = generateCSRF();
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Excluir Ficha</title>
  <link rel="stylesheet" href="/style/main-style.css">
</head>

<body>
  <div class="app-shell">
    <header class="site-topbar">
      <div>
        <h1 class="brand-title">Arquivo do Investigador</h1>
        <p class="brand-sub">Remocao de dossie</p>
      </div>
      <a class="btn btn-outline" href="/sheet_view.php?id=<?php echo (int) $sheetId; ?>">Voltar</a>
    </header>

    <main class="page">
      <section class="news-shell card">
        <div class="hero">
          <h2 class="headline">Excluir ficha</h2>
          <p>Tem certeza que deseja excluir <strong><?php echo htmlspecialchars((string) $sheet['character_name']); ?></strong>?</p>
          <p class="muted">Essa acao nao pode ser desfeita.</p>
        </div>

        <form method="post" action="/sheet_delete.php">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
          <input type="hidden" name="id" value="<?php echo (int) $sheetId; ?>">

          <div class="news-actions">
            <button type="submit" class="btn">Confirmar exclusao</button>
            <a class="btn btn-outline" href="/sheet_view.php?id=<?php echo (int) $sheetId; ?>">Cancelar</a>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>

</html>