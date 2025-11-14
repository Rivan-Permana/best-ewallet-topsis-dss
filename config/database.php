<?php
/**
 * Database Configuration
 * SQLite database connection
 */

class Database {
    private static $instance = null;
    private $connection;
    private $dbPath;

    private function __construct() {
        $this->dbPath = __DIR__ . '/../database/topsis.db';

        try {
            // Create database directory if not exists
            $dir = dirname($this->dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            // Create SQLite connection
            $this->connection = new PDO("sqlite:" . $this->dbPath);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Enable foreign keys
            $this->connection->exec("PRAGMA foreign_keys = ON");

            // Initialize database if tables don't exist
            $this->initializeDatabase();

        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    private function initializeDatabase() {
        // Check if tables exist
        $stmt = $this->connection->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='criteria'"
        );

        // Only initialize if database is completely empty
        if ($stmt->rowCount() == 0) {
            try {
                // Read init.sql
                $initSQL = file_get_contents(__DIR__ . '/../database/init.sql');

                // Execute the SQL
                $this->connection->exec($initSQL);
            } catch (PDOException $e) {
                // If initialization fails, just log it but continue
                // Database might already be partially initialized
                error_log("Database initialization warning: " . $e->getMessage());
            }
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollBack() {
        return $this->connection->rollBack();
    }
}

// Helper function to get database instance
function db() {
    return Database::getInstance();
}
