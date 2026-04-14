<?php

require_once 'session.php';

function generateCSRF(): string
{
  startSecureSession();

  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }

  return $_SESSION['csrf'];
}

function validateCSRF(string $token): bool
{
  startSecureSession();

  if (!isset($_SESSION['csrf'])) {
    return false;
  }

  return hash_equals($_SESSION['csrf'], $token);
}
