<?php

declare(strict_types=1);

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/csrf.php';
require_once __DIR__ . '/php/sheets.php';

requireAuth();
startSecureSession();

$userId = (int) $_SESSION['user_id'];
$sheetId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

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

$csrf = generateCSRF();
$errors = [];

$data = $sheet;
$data['skills_text'] = parseSkillsJsonToText($sheet['skills_json'] ?? '{}');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!validateCSRF($_POST['csrf'] ?? '')) {
    $errors[] = 'Token invalido. Recarregue a pagina e tente novamente.';
  } else {
    $data = buildSheetDataFromInput($_POST);
    $data['skills_text'] = trim((string) ($_POST['skills_text'] ?? ''));
    $errors = validateSheetData($data);

    if ($errors === []) {
      updateSheet($sheetId, $userId, $data);
      $_SESSION['success'] = 'Ficha atualizada com sucesso.';
      header('Location: /sheet_view.php?id=' . $sheetId);
      exit;
    }
  }
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
  <title>Ficha - <?php echo htmlspecialchars((string) ($data['character_name'] ?? 'Investigador')); ?></title>
  <link rel="stylesheet" href="/style/main-style.css">
</head>

<body>
  <div class="app-shell">
    <header class="site-topbar">
      <div>
        <h1 class="brand-title">Arquivo do Investigador</h1>
        <p class="brand-sub">Dossie em edição</p>
      </div>
      <div class="news-actions">
        <a class="btn btn-outline" href="/dashboard.php">Painel</a>
        <a class="btn btn-outline" href="/sheet_delete.php?id=<?php echo (int) $sheetId; ?>">Excluir</a>
      </div>
    </header>

    <main class="page">
      <section class="news-shell card">
        <div class="hero">
          <h2 class="headline"><?php echo htmlspecialchars((string) ($data['character_name'] ?? 'Investigador')); ?></h2>
          <p>Ficha #<?php echo (int) $sheetId; ?> aberta para visualizacao e edicao no mesmo lugar.</p>
        </div>

        <div class="form-toolbar">
          <span class="status-chip" id="autosave-status">Status: aguardando alteracoes...</span>
        </div>

        <?php if ($success): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars((string) $success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars((string) $error); ?></div>
        <?php endif; ?>

        <?php if ($errors !== []): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $message): ?>
              <p><?php echo htmlspecialchars($message); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="post" action="/sheet_view.php?id=<?php echo (int) $sheetId; ?>" id="sheet-form">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
          <input type="hidden" name="sheet_id" value="<?php echo (int) $sheetId; ?>">

          <?php require __DIR__ . '/php/sheet_form_fields.php'; ?>

          <div class="form-toolbar">
            <button type="submit" class="btn">Salvar agora</button>
            <span class="muted">Autosave a cada 20 segundos enquanto houver alteracoes.</span>
          </div>
        </form>
      </section>
    </main>
  </div>

  <script src="/js/sheet-enhancer.js"></script>
  <script>
    (() => {
      const form = document.getElementById('sheet-form');
      const status = document.getElementById('autosave-status');
      let dirty = false;
      let saving = false;

      form.addEventListener('input', () => {
        dirty = true;
        status.textContent = 'Status: alteracoes pendentes...';
      });

      const save = async () => {
        if (!dirty || saving) {
          return;
        }

        saving = true;
        status.textContent = 'Status: salvando automaticamente...';

        try {
          const formData = new FormData(form);
          const response = await fetch('/php/sheet_autosave.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
          });

          const result = await response.json();
          if (result.ok) {
            dirty = false;
            status.textContent = 'Status: salvo em ' + new Date().toLocaleTimeString();
          } else {
            status.textContent = 'Status: erro no autosave.';
          }
        } catch (error) {
          status.textContent = 'Status: erro de conexao no autosave.';
        } finally {
          saving = false;
        }
      };

      setInterval(save, 20000);
    })();
  </script>
</body>

</html>