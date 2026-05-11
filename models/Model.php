<?php

namespace Models;

use App\DB\Database;

abstract class Model {

    protected string $table = '';
    protected string $primaryKey = 'id';

    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(int $limit = 0, int $offset = 0): array {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($limit > 0) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        $this->db->query($sql);
        return $this->db->fetchAll();
    }

    public function count(): int {
        $this->db->query("SELECT COUNT(*) AS total FROM `{$this->table}`");
        $result = $this->db->fetchAll();
        return (int) ($result[0]['total'] ?? 0);
    }

    public function getById(int $id): ?array {
        $this->db->query(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
        $result = $this->db->fetchAll();
        return $result[0] ?? null;
    }

    public function getBy(string $column, mixed $value): array {
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        $this->db->query(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = ?",
            [$value]
        );
        return $this->db->fetchAll();
    }

    public function countBy(string $column, mixed $value): int {
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        $this->db->query(
            "SELECT COUNT(*) AS total FROM `{$this->table}` WHERE `{$column}` = ?",
            [$value]
        );
        $result = $this->db->fetchAll();
        return (int) ($result[0]['total'] ?? 0);
    }

    public function exists(int $id): bool {
        return $this->getById($id) !== null;
    }

    public function insert(array $data): int {
        $columns      = array_map(fn($col) => preg_replace('/[^a-zA-Z0-9_]/', '', $col), array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $columnList   = '`' . implode('`, `', $columns) . '`';
        $this->db->query(
            "INSERT INTO `{$this->table}` ({$columnList}) VALUES ({$placeholders})",
            array_values($data)
        );
        return (int) $this->db->getId();
    }

    public function update(int $id, array $data): int {
        $columns = array_map(fn($col) => preg_replace('/[^a-zA-Z0-9_]/', '', $col), array_keys($data));
        $sets    = implode(', ', array_map(fn($col) => "`{$col}` = ?", $columns));
        $this->db->query(
            "UPDATE `{$this->table}` SET {$sets} WHERE `{$this->primaryKey}` = ?",
            [...array_values($data), $id]
        );
        return $this->db->rowCount();
    }

    public function delete(int $id): bool {
        $this->db->query(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
        return $this->db->rowCount() > 0;
    }

}