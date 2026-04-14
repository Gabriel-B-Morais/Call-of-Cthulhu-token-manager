<?php

declare(strict_types=1);

// Arquivo de configuração e conexão com o banco de dados usando PDO

namespace App\Database;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

class Database
{

  private static ?PDO $pdo = null;
  private static bool $envLoaded = false;

  private function __construct() {}

  public static function getConnection(): PDO
  {
    try {
      if (self::$pdo instanceof PDO) {
        return self::$pdo;
      }

      self::loadEnvFromFile();

      $host = self::getConfigValue('DB_HOST');
      $port = self::getConfigValue('DB_PORT');
      $name = self::getConfigValue('DB_DATABASE');
      $user = self::getConfigValue('DB_USERNAME');
      $pass = self::getConfigValue('DB_PASSWORD');

      if ($host === '' || $port === '' || $name === '' || $user === '') {
        throw new RuntimeException('Configuracao de banco incompleta.');
      }

      $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

      self::$pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ]);

      return self::$pdo;
    } catch (PDOException $e) {
      throw new RuntimeException('Erro ao conectar ao banco de dados: ' . $e->getMessage());
    }
  }

  private static function getConfigValue(string $key): string
  {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: '';
    return trim((string) $value);
  }

  private static function loadEnvFromFile(): void
  {
    if (self::$envLoaded) {
      return;
    }

    self::$envLoaded = true;

    $root = dirname(__DIR__);
    $candidates = [
      $root . '/.env.prod',
      $root . '/.env',
    ];

    foreach ($candidates as $file) {
      if (!is_file($file) || !is_readable($file)) {
        continue;
      }

      $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      if ($lines === false) {
        continue;
      }

      foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
          continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ($key === '' || isset($_ENV[$key])) {
          continue;
        }

        $_ENV[$key] = $value;
      }
    }
  }

  // Helpers para ajudar nas quarys

  public static function query(string $sql, array $params = []): PDOStatement
  {
    try {
      $pdo = self::getConnection();
      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
      return $stmt;
    } catch (PDOException $e) {
      throw new RuntimeException('Erro ao executar consulta: ' . $e->getMessage());
    }
  }

  public static function fetch(string $sql, array $params = []): ?array
  {
    try {
      $stmt = self::query($sql, $params);
      return $stmt->fetch() ?: null;
    } catch (RuntimeException $e) {
      throw new RuntimeException('Erro ao buscar dados: ' . $e->getMessage());
    }
  }

  public static function fetchAll(string $sql, array $params = []): ?array
  {
    try {
      $stmt = self::query($sql, $params);
      return $stmt->fetchAll() ?: null;
    } catch (RuntimeException $e) {
      throw new RuntimeException('Erro ao buscar dados: ' . $e->getMessage());
    }
  }
}
