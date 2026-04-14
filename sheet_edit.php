<?php

declare(strict_types=1);

$sheetId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($sheetId && $sheetId > 0) {
  header('Location: /sheet_view.php?id=' . $sheetId);
  exit;
}

header('Location: /dashboard.php');
exit;
