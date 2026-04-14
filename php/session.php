<?php

function startSecureSession() : void {
  if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
      'lifetime' => 0,
      'path' => '/',
      'secure' => isset($_SERVER['HTTPS']),
      'httponly' => true,
      'samesite' => 'Strict'
    ]);

    session_start();
  }
}