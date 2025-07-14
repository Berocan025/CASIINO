<?php
/**
 * Database Connection Class
 * Geliştirici: BERAT K
 * SQLite database connection and management
 */

class Database {
    private $connection;
    private $db_path;
    
    public function __construct($db_path = null) {
        $this->db_path = $db_path ?: DB_PATH;
    }
    
    /**
     * Get PDO database connection
     * @return PDO
     * @throws Exception
     */
    public function getConnection() {
        if ($this->connection === null) {
            try {
                // Create database directory if it doesn't exist
                $db_dir = dirname($this->db_path);
                if (!is_dir($db_dir)) {
                    if (!mkdir($db_dir, 0755, true)) {
                        throw new Exception("Failed to create database directory: $db_dir");
                    }
                }
                
                // Create SQLite connection
                $this->connection = new PDO(
                    'sqlite:' . $this->db_path,
                    null,
                    null,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_PERSISTENT => false
                    ]
                );
                
                // Enable foreign key constraints
                $this->connection->exec('PRAGMA foreign_keys = ON');
                
                // Set timeout for database locks
                $this->connection->exec('PRAGMA busy_timeout = 30000');
                
                // Enable WAL mode for better concurrency
                $this->connection->exec('PRAGMA journal_mode = WAL');
                
            } catch (Exception $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
        
        return $this->connection;
    }
    
    /**
     * Execute a query with parameters
     * @param string $query
     * @param array $params
     * @return PDOStatement
     * @throws Exception
     */
    public function query($query, $params = []) {
        try {
            $stmt = $this->getConnection()->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (Exception $e) {
            error_log("Database query failed: " . $e->getMessage() . " | Query: " . $query);
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get single row from database
     * @param string $query
     * @param array $params
     * @return array|false
     */
    public function fetchRow($query, $params = []) {
        try {
            $stmt = $this->query($query, $params);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Database fetchRow failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all rows from database
     * @param string $query
     * @param array $params
     * @return array
     */
    public function fetchAll($query, $params = []) {
        try {
            $stmt = $this->query($query, $params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Database fetchAll failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Insert data into database
     * @param string $table
     * @param array $data
     * @return int|false Last insert ID
     */
    public function insert($table, $data) {
        try {
            $columns = array_keys($data);
            $placeholders = ':' . implode(', :', $columns);
            $columns_str = implode(', ', $columns);
            
            $query = "INSERT INTO {$table} ({$columns_str}) VALUES ({$placeholders})";
            $this->query($query, $data);
            
            return $this->getConnection()->lastInsertId();
        } catch (Exception $e) {
            error_log("Database insert failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update data in database
     * @param string $table
     * @param array $data
     * @param array $where
     * @return bool
     */
    public function update($table, $data, $where) {
        try {
            $set_clause = [];
            foreach (array_keys($data) as $column) {
                $set_clause[] = "{$column} = :{$column}";
            }
            $set_str = implode(', ', $set_clause);
            
            $where_clause = [];
            $where_params = [];
            foreach ($where as $column => $value) {
                $where_clause[] = "{$column} = :where_{$column}";
                $where_params["where_{$column}"] = $value;
            }
            $where_str = implode(' AND ', $where_clause);
            
            $query = "UPDATE {$table} SET {$set_str} WHERE {$where_str}";
            $params = array_merge($data, $where_params);
            
            $stmt = $this->query($query, $params);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Database update failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete data from database
     * @param string $table
     * @param array $where
     * @return bool
     */
    public function delete($table, $where) {
        try {
            $where_clause = [];
            foreach (array_keys($where) as $column) {
                $where_clause[] = "{$column} = :{$column}";
            }
            $where_str = implode(' AND ', $where_clause);
            
            $query = "DELETE FROM {$table} WHERE {$where_str}";
            $stmt = $this->query($query, $where);
            
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Database delete failed: " . $e->getMessage());
            return false;
        }
    }
    
        /**
     * Count rows in table
     * @param string $table
     * @param array $where
     * @return int
     */
    public function count($table, $where = []) {
        try {
            $query = "SELECT COUNT(*) as count FROM {$table}";
            $params = [];
            
            if (!empty($where)) {
                $where_clause = [];
                foreach (array_keys($where) as $column) {
                    $where_clause[] = "{$column} = :{$column}";
                }
                $query .= " WHERE " . implode(' AND ', $where_clause);
                $params = $where;
            }
            
            $result = $this->fetchRow($query, $params);
            return (int) $result['count'];
        } catch (Exception $e) {
            error_log("Database count failed: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Find single record from table
     * @param string $table
     * @param array $where
     * @return array|false
     */
    public function find($table, $where = []) {
        try {
            $query = "SELECT * FROM {$table}";
            $params = [];
            
            if (!empty($where)) {
                $where_clause = [];
                foreach (array_keys($where) as $column) {
                    $where_clause[] = "{$column} = :{$column}";
                }
                $query .= " WHERE " . implode(' AND ', $where_clause);
                $params = $where;
            }
            
            $query .= " LIMIT 1";
            
            return $this->fetchRow($query, $params);
        } catch (Exception $e) {
            error_log("Database find failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find all records from table
     * @param string $table
     * @param array $where
     * @param string $order_by
     * @return array
     */
    public function findAll($table, $where = [], $order_by = '') {
        try {
            $query = "SELECT * FROM {$table}";
            $params = [];
            
            if (!empty($where)) {
                $where_clause = [];
                foreach (array_keys($where) as $column) {
                    $where_clause[] = "{$column} = :{$column}";
                }
                $query .= " WHERE " . implode(' AND ', $where_clause);
                $params = $where;
            }
            
            if (!empty($order_by)) {
                $query .= " ORDER BY " . $order_by;
            }
            
            return $this->fetchAll($query, $params);
        } catch (Exception $e) {
            error_log("Database findAll failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Begin transaction
     * @return bool
     */
    public function beginTransaction() {
        try {
            return $this->getConnection()->beginTransaction();
        } catch (Exception $e) {
            error_log("Begin transaction failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Commit transaction
     * @return bool
     */
    public function commit() {
        try {
            return $this->getConnection()->commit();
        } catch (Exception $e) {
            error_log("Commit transaction failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Rollback transaction
     * @return bool
     */
    public function rollback() {
        try {
            return $this->getConnection()->rollback();
        } catch (Exception $e) {
            error_log("Rollback transaction failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get table exists status
     * @param string $table
     * @return bool
     */
    public function tableExists($table) {
        try {
            $query = "SELECT name FROM sqlite_master WHERE type='table' AND name=:table";
            $result = $this->fetchRow($query, ['table' => $table]);
            return !empty($result);
        } catch (Exception $e) {
            error_log("Table exists check failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Backup database
     * @param string $backup_path
     * @return bool
     */
    public function backup($backup_path) {
        try {
            if (!file_exists($this->db_path)) {
                return false;
            }
            
            $backup_dir = dirname($backup_path);
            if (!is_dir($backup_dir)) {
                mkdir($backup_dir, 0755, true);
            }
            
            return copy($this->db_path, $backup_path);
        } catch (Exception $e) {
            error_log("Database backup failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Close database connection
     */
    public function close() {
        $this->connection = null;
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        $this->close();
    }
}
?>