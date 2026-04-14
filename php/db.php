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

  private function __construct() {}

  public static function getConnection(): PDO
  {
    try {
      if (self::$pdo instanceof PDO) {
        return self::$pdo;
      }

      $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '';
      $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '';
      $name = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: '';
      $user = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: '';
      $pass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';

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

  public static function fetch(string $sql, array $params = []) : ?array
  {
    try {
      $stmt = self::query($sql, $params);
      return $stmt->fetch() ?: null;
    } catch (RuntimeException $e) {
      throw new RuntimeException('Erro ao buscar dados: ' . $e->getMessage());
    }
  }

  public static function fetchAll(string $sql, array $params = []) : ?array
  {
    try {
      $stmt = self::query($sql, $params);
      return $stmt->fetchAll() ?: null;
    } catch (RuntimeException $e) {
      throw new RuntimeException('Erro ao buscar dados: ' . $e->getMessage());
    }
  }
}
