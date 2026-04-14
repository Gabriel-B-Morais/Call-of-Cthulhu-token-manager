<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/sheets.php';

header('Content-Type: application/json; charset=utf-8');

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'message' => 'Metodo invalido']);
  exit;
}

if (!validateCSRF($_POST['csrf'] ?? '')) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'message' => 'Token invalido']);
  exit;
}

$sheetId = filter_input(INPUT_POST, 'sheet_id', FILTER_VALIDATE_INT);
if (!$sheetId || $sheetId < 1) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'message' => 'Ficha invalida']);
  exit;
}

startSecureSession();
$userId = (int) $_SESSION['user_id'];

if (findSheetByIdAndUser($sheetId, $userId) === null) {
  http_response_code(404);
  echo json_encode(['ok' => false, 'message' => 'Ficha nao encontrada']);
  exit;
}

$data = buildSheetDataFromInput($_POST);
$errors = validateSheetData($data);
if ($errors !== []) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'message' => $errors[0]]);
  exit;
}

updateSheet($sheetId, $userId, $data);

echo json_encode(['ok' => true]);
