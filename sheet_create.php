<?php

declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/csrf.php';
require_once __DIR__ . '/php/sheets.php';

use App\Database\Database;

requireAuth();

startSecureSession();

$csrf = generateCSRF();
$userId = (int) $_SESSION['user_id'];

// Verificar se o usuário é testuser, se for limitar a criação de fichas a 5
if ($_SESSION['username'] === 'testuser') {
  $sheetCount = Database::fetch('SELECT COUNT(*) AS cont FROM character_sheets WHERE user_id = :user_id', ['user_id' => $userId])['cont'] ?? 0;
  if ($sheetCount >= 5) {
    $_SESSION['error'] = 'Limite de fichas atingido para o usuário de teste. Por favor, use outro usuário para criar mais fichas.';
    header('Location: /dashboard.php');
    exit;
  }
}

$data = defaultSheetData();
$data['skills_text'] = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!validateCSRF($_POST['csrf'] ?? '')) {
    $errors[] = 'Token invalido. Recarregue a pagina e tente novamente.';
  } else {
    $data = buildSheetDataFromInput($_POST);
    $data['skills_text'] = trim((string) ($_POST['skills_text'] ?? ''));
    $errors = validateSheetData($data);

    if ($errors === []) {
      $sheetId = createSheet($userId, $data);
      $_SESSION['success'] = 'Ficha criada com sucesso.';
      header('Location: /sheet_view.php?id=' . $sheetId);
      exit;
    }
  }
}
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nova Ficha - Call of Cthulhu</title>
  <link rel="stylesheet" href="/style/main-style.css">
</head>

<body>
  <div class="app-shell">
    <header class="site-topbar">
      <div>
        <h1 class="brand-title">Arquivo do Investigador</h1>
        <p class="brand-sub">Criacao de ficha</p>
      </div>
      <div class="news-actions">
        <a class="btn btn-outline" href="/dashboard.php">Voltar ao painel</a>
      </div>
    </header>

    <main class="page">
      <section class="news-shell card">
        <div class="hero">
          <h2 class="headline">Nova Ficha de Investigador</h2>
          <p>Preencha os dados, gere atributos automaticamente ou use o modo manual.</p>
        </div>

        <?php if ($errors !== []): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
              <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="post" action="/sheet_create.php" id="sheet-form">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">

          <?php require __DIR__ . '/php/sheet_form_fields.php'; ?>

          <div class="form-toolbar">
            <button type="submit" class="btn">Criar ficha</button>
            <a class="btn btn-outline" href="/dashboard.php">Cancelar</a>
          </div>
        </form>
      </section>
    </main>
  </div>
  <script src="/js/sheet-enhancer.js"></script>
</body>

</html>