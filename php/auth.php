<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';

use App\Database\Database;

function login(string $username, string $password): bool
{
  startSecureSession();

  try {
    $user = Database::fetch('SELECT id, password_hash FROM users WHERE username = :username', ['username' => $username]);
  } catch (\Throwable $e) {
    return false;
  }

  if (!$user) {
    return false;
  }

  if (!password_verify($password, $user['password_hash'])) {
    return false;
  }

  session_regenerate_id(true);

  $_SESSION['user_id'] = $user['id'];
  $_SESSION['username'] = $username;

  return true;
}

function logout(): void
{
  startSecureSession();

  $_SESSION = [];

  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
      session_name(),
      '',
      time() - 42000,
      $params['path'],
      $params['domain'],
      $params['secure'],
      $params['httponly']
    );
  }

  session_destroy();
}

function requireAuth(): void
{
  startSecureSession();

  if (!isset($_SESSION['user_id'])) {
    header("Location: /");
    exit;
  }
}
