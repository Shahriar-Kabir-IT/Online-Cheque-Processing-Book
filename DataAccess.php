<?php
require_once __DIR__ . '/config.php';

/**
 * PDO-based data access layer.
 * Matches the interface described in SYSTEM_DOCUMENTATION.md.
 */
class DataAccess
{
    /** @var PDO */
    private $pdo;

    public function __construct($host = null, $user = null, $pass = null, $db = null)
    {
        $host = $host ?? DB_HOST;
        $user = $user ?? DB_USER;
        $pass = $pass ?? DB_PASS;
        $db   = $db ?? DB_NAME;

        $dsn = "mysql:host={$host};dbname={$db};charset=" . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function Execute(string $sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function Insert(string $sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $this->pdo->lastInsertId();
    }

    public function affectedRows(PDOStatement $stmt): int
    {
        return $stmt->rowCount();
    }

    public function fetchAll(PDOStatement $result): array
    {
        return $result->fetchAll();
    }

    public function fetchArray(PDOStatement $result)
    {
        return $result->fetch();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
}

