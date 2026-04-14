<?php

require_once 'auth.php';
require_once 'csrf.php';

startSecureSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!validateCSRF($_POST['csrf'] ?? '')) {
    $_SESSION['error'] = "Token inválido.";
    header("Location: /");
    exit;
  }

  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if (login($username, $password)) {
    header("Location: /dashboard.php");
    exit;
  }

  $_SESSION['error'] = "Usuário ou senha inválidos.";
  header("Location: /");
}

header('Location: /');
exit;
