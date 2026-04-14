<?php

require_once __DIR__ . '/php/session.php';
require_once __DIR__ . '/php/csrf.php';

startSecureSession();

if (isset($_SESSION['user_id'])) {
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
  <title>Call of Cthulhu - Login</title>
  <link rel="stylesheet" href="/style/main-style.css">
</head>

<body>
  <div class="app-shell">
    <header class="site-topbar">
      <div>
        <h1 class="brand-title">Arquivo do Investigador</h1>
        <p class="brand-sub">Sistema de fichas Call of Cthulhu</p>
      </div>
    </header>

    <main class="page">
      <section class="news-shell card">
        <div class="hero">
          <h2 class="headline">Acesso de Investigador</h2>
          <p>Entre para gerenciar fichas durante suas sessoes de jogo.</p>
        </div>

        <div class="form-login">
          <form action="/php/login.php" method="post">
            <label for="username">Usuario</label>
            <input type="text" id="username" name="username" required>

            <div class="containerInput">
              <label for="password">Senha</label>
              <input type="password" id="password" name="password" required>
            </div>

            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">

            <?php
            if (isset($_SESSION['error'])) {
              echo "<div class='error-message'>" . htmlspecialchars($_SESSION['error']) . "</div>";
              unset($_SESSION['error']);
            }
            ?>

            <button type="submit">Entrar</button>
          </form>
        </div>
        <p class="meta">Call of Cthulhu é marca registrada da Chaosium Inc. e é usado com permissão (www.chaosium.com). Call of Cthulhu 7ª Edição © 1981–2024 Chaosium Inc. Este é um projeto sem fins lucrativos e não oficial, não sendo endossado ou afiliado à Chaosium Inc. ou à New Order Editora.</p>
        <p class="meta">Ainda não foi criado um sistema de cadastro, portanto, para acessar o sistema, use as seguintes credenciais de teste: <br>Usuário: <code>testuser</code><br>Senha: <code>testpassword</code></p>
      </section>
    </main>
  </div>
</body>

</html>