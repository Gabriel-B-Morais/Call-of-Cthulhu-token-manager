<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

use App\Database\Database;

function defaultSheetData(): array
{
  return [
    'character_name' => '',
    'occupation' => '',
    'age' => null,
    'residence' => '',
    'birthplace' => '',
    'str_val' => 50,
    'con_val' => 50,
    'dex_val' => 50,
    'app_val' => 50,
    'pow_val' => 50,
    'int_val' => 50,
    'siz_val' => 50,
    'edu_val' => 50,
    'hp' => null,
    'max_hp' => null,
    'sanity' => null,
    'max_sanity' => null,
    'magic_points' => null,
    'max_magic_points' => null,
    'move_rate' => null,
    'damage_bonus' => '',
    'build' => '',
    'skills_json' => '{}',
    'equipment_text' => '',
    'notes_text' => '',
  ];
}

function normalizeText(?string $value, int $maxLen): string
{
  $text = trim((string) $value);
  if ($text === '') {
    return '';
  }

  return substr($text, 0, $maxLen);
}

function normalizeNullableInt(mixed $value, int $min, int $max): ?int
{
  if ($value === null || $value === '') {
    return null;
  }

  if (!is_numeric($value)) {
    return null;
  }

  $number = (int) $value;
  if ($number < $min) {
    return $min;
  }

  if ($number > $max) {
    return $max;
  }

  return $number;
}

function parseSkillsTextToJson(string $skillsText): string
{
  $lines = preg_split('/\r\n|\r|\n/', trim($skillsText)) ?: [];
  $skills = [];

  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') {
      continue;
    }

    $parts = explode(':', $line, 2);
    if (count($parts) !== 2) {
      continue;
    }

    $name = trim($parts[0]);
    $value = trim($parts[1]);

    if ($name === '' || $value === '' || !is_numeric($value)) {
      continue;
    }

    $skills[$name] = max(0, min(100, (int) $value));
  }

  return json_encode($skills, JSON_UNESCAPED_UNICODE) ?: '{}';
}

function parseSkillsJsonToText(?string $skillsJson): string
{
  if ($skillsJson === null || trim($skillsJson) === '') {
    return '';
  }

  $decoded = json_decode($skillsJson, true);
  if (!is_array($decoded)) {
    return '';
  }

  $lines = [];
  foreach ($decoded as $skill => $value) {
    $name = trim((string) $skill);
    if ($name === '' || !is_numeric($value)) {
      continue;
    }

    $lines[] = $name . ': ' . (int) $value;
  }

  return implode("\n", $lines);
}

function calculateDamageBonusAndBuild(int $str, int $siz): array
{
  $sum = $str + $siz;

  if ($sum <= 64) {
    return ['damage_bonus' => '-2', 'build' => '-2'];
  }

  if ($sum <= 84) {
    return ['damage_bonus' => '-1', 'build' => '-1'];
  }

  if ($sum <= 124) {
    return ['damage_bonus' => '0', 'build' => '0'];
  }

  if ($sum <= 164) {
    return ['damage_bonus' => '+1d4', 'build' => '1'];
  }

  if ($sum <= 204) {
    return ['damage_bonus' => '+1d6', 'build' => '2'];
  }

  if ($sum <= 284) {
    return ['damage_bonus' => '+2d6', 'build' => '3'];
  }

  return ['damage_bonus' => '+3d6', 'build' => '4'];
}

function calculateMoveRate(int $str, int $dex, int $siz, ?int $age): int
{
  $mov = 8;

  if ($str < $siz && $dex < $siz) {
    $mov = 7;
  } elseif ($str > $siz && $dex > $siz) {
    $mov = 9;
  }

  if ($age !== null) {
    if ($age >= 40 && $age < 50) {
      $mov -= 1;
    } elseif ($age >= 50 && $age < 60) {
      $mov -= 2;
    } elseif ($age >= 60 && $age < 70) {
      $mov -= 3;
    } elseif ($age >= 70 && $age < 80) {
      $mov -= 4;
    } elseif ($age >= 80) {
      $mov -= 5;
    }
  }

  return max(1, $mov);
}

function buildSheetDataFromInput(array $input): array
{
  $data = defaultSheetData();

  $data['character_name'] = normalizeText($input['character_name'] ?? '', 80);
  $data['occupation'] = normalizeText($input['occupation'] ?? '', 80);
  $data['age'] = normalizeNullableInt($input['age'] ?? null, 1, 120);
  $data['residence'] = normalizeText($input['residence'] ?? '', 120);
  $data['birthplace'] = normalizeText($input['birthplace'] ?? '', 120);

  $data['str_val'] = normalizeNullableInt($input['str_val'] ?? 50, 1, 200) ?? 50;
  $data['con_val'] = normalizeNullableInt($input['con_val'] ?? 50, 1, 200) ?? 50;
  $data['dex_val'] = normalizeNullableInt($input['dex_val'] ?? 50, 1, 200) ?? 50;
  $data['app_val'] = normalizeNullableInt($input['app_val'] ?? 50, 1, 200) ?? 50;
  $data['pow_val'] = normalizeNullableInt($input['pow_val'] ?? 50, 1, 200) ?? 50;
  $data['int_val'] = normalizeNullableInt($input['int_val'] ?? 50, 1, 200) ?? 50;
  $data['siz_val'] = normalizeNullableInt($input['siz_val'] ?? 50, 1, 200) ?? 50;
  $data['edu_val'] = normalizeNullableInt($input['edu_val'] ?? 50, 1, 200) ?? 50;

  $data['hp'] = normalizeNullableInt($input['hp'] ?? null, 0, 99);
  $data['max_hp'] = normalizeNullableInt($input['max_hp'] ?? null, 0, 99);
  $data['sanity'] = normalizeNullableInt($input['sanity'] ?? null, 0, 999);
  $data['max_sanity'] = normalizeNullableInt($input['max_sanity'] ?? null, 0, 999);
  $data['magic_points'] = normalizeNullableInt($input['magic_points'] ?? null, 0, 99);
  $data['max_magic_points'] = normalizeNullableInt($input['max_magic_points'] ?? null, 0, 99);
  $data['move_rate'] = normalizeNullableInt($input['move_rate'] ?? null, 1, 20);

  $skillsText = trim((string) ($input['skills_text'] ?? ''));
  $data['skills_json'] = parseSkillsTextToJson($skillsText);

  $data['equipment_text'] = normalizeText($input['equipment_text'] ?? '', 8000);
  $data['notes_text'] = normalizeText($input['notes_text'] ?? '', 8000);

  if ($data['hp'] === null) {
    $data['hp'] = (int) floor(($data['con_val'] + $data['siz_val']) / 10);
  }
  if ($data['max_hp'] === null) {
    $data['max_hp'] = (int) floor(($data['con_val'] + $data['siz_val']) / 10);
  }

  if ($data['sanity'] === null) {
    $data['sanity'] = $data['pow_val'];
  }
  if ($data['max_sanity'] === null) {
    $data['max_sanity'] = $data['pow_val'];
  }

  if ($data['magic_points'] === null) {
    $data['magic_points'] = (int) floor($data['pow_val'] / 5);
  }
  if ($data['max_magic_points'] === null) {
    $data['max_magic_points'] = (int) floor($data['pow_val'] / 5);
  }

  if ($data['move_rate'] === null) {
    $data['move_rate'] = calculateMoveRate($data['str_val'], $data['dex_val'], $data['siz_val'], $data['age']);
  }

  $bonusAndBuild = calculateDamageBonusAndBuild($data['str_val'], $data['siz_val']);
  $data['damage_bonus'] = $bonusAndBuild['damage_bonus'];
  $data['build'] = $bonusAndBuild['build'];

  return $data;
}

function validateSheetData(array $data): array
{
  $errors = [];

  if ($data['character_name'] === '') {
    $errors[] = 'Nome do personagem e obrigatorio.';
  }

  return $errors;
}

function sheetPersistenceData(array $data): array
{
  return [
    'character_name' => $data['character_name'] ?? '',
    'occupation' => $data['occupation'] ?? '',
    'age' => $data['age'] ?? null,
    'residence' => $data['residence'] ?? '',
    'birthplace' => $data['birthplace'] ?? '',
    'str_val' => $data['str_val'] ?? 50,
    'con_val' => $data['con_val'] ?? 50,
    'dex_val' => $data['dex_val'] ?? 50,
    'app_val' => $data['app_val'] ?? 50,
    'pow_val' => $data['pow_val'] ?? 50,
    'int_val' => $data['int_val'] ?? 50,
    'siz_val' => $data['siz_val'] ?? 50,
    'edu_val' => $data['edu_val'] ?? 50,
    'hp' => $data['hp'] ?? null,
    'max_hp' => $data['max_hp'] ?? null,
    'sanity' => $data['sanity'] ?? null,
    'max_sanity' => $data['max_sanity'] ?? null,
    'magic_points' => $data['magic_points'] ?? null,
    'max_magic_points' => $data['max_magic_points'] ?? null,
    'move_rate' => $data['move_rate'] ?? null,
    'damage_bonus' => $data['damage_bonus'] ?? '',
    'build' => $data['build'] ?? '',
    'skills_json' => $data['skills_json'] ?? '{}',
    'equipment_text' => $data['equipment_text'] ?? '',
    'notes_text' => $data['notes_text'] ?? '',
  ];
}

function listSheetsByUser(int $userId): array
{
  $rows = Database::fetchAll(
    'SELECT id, character_name, occupation, hp, max_hp, sanity, max_sanity, updated_at
     FROM character_sheets
     WHERE user_id = :user_id
     ORDER BY updated_at DESC, id DESC',
    ['user_id' => $userId]
  );

  return $rows ?? [];
}

function findSheetByIdAndUser(int $sheetId, int $userId): ?array
{
  return Database::fetch(
    'SELECT *
     FROM character_sheets
     WHERE id = :id AND user_id = :user_id
     LIMIT 1',
    [
      'id' => $sheetId,
      'user_id' => $userId,
    ]
  );
}

function createSheet(int $userId, array $data): int
{
  $payload = sheetPersistenceData($data);

  Database::query(
    'INSERT INTO character_sheets (
      user_id, character_name, occupation, age, residence, birthplace,
      str_val, con_val, dex_val, app_val, pow_val, int_val, siz_val, edu_val,
      hp, max_hp, sanity, max_sanity, magic_points, max_magic_points,
      move_rate, damage_bonus, build, skills_json, equipment_text, notes_text
    ) VALUES (
      :user_id, :character_name, :occupation, :age, :residence, :birthplace,
      :str_val, :con_val, :dex_val, :app_val, :pow_val, :int_val, :siz_val, :edu_val,
      :hp, :max_hp, :sanity, :max_sanity, :magic_points, :max_magic_points,
      :move_rate, :damage_bonus, :build, :skills_json, :equipment_text, :notes_text
    )',
    array_merge($payload, ['user_id' => $userId])
  );

  return (int) Database::getConnection()->lastInsertId();
}

function updateSheet(int $sheetId, int $userId, array $data): bool
{
  $payload = sheetPersistenceData($data);

  $stmt = Database::query(
    'UPDATE character_sheets SET
      character_name = :character_name,
      occupation = :occupation,
      age = :age,
      residence = :residence,
      birthplace = :birthplace,
      str_val = :str_val,
      con_val = :con_val,
      dex_val = :dex_val,
      app_val = :app_val,
      pow_val = :pow_val,
      int_val = :int_val,
      siz_val = :siz_val,
      edu_val = :edu_val,
      hp = :hp,
      max_hp = :max_hp,
      sanity = :sanity,
      max_sanity = :max_sanity,
      magic_points = :magic_points,
      max_magic_points = :max_magic_points,
      move_rate = :move_rate,
      damage_bonus = :damage_bonus,
      build = :build,
      skills_json = :skills_json,
      equipment_text = :equipment_text,
      notes_text = :notes_text
    WHERE id = :id AND user_id = :user_id',
    array_merge($payload, ['id' => $sheetId, 'user_id' => $userId])
  );

  return $stmt->rowCount() > 0;
}

function deleteSheet(int $sheetId, int $userId): bool
{
  $stmt = Database::query(
    'DELETE FROM character_sheets WHERE id = :id AND user_id = :user_id',
    [
      'id' => $sheetId,
      'user_id' => $userId,
    ]
  );

  return $stmt->rowCount() > 0;
}
