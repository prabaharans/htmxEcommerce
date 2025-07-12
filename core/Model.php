<?php

class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = DatabaseConfig::getConnection();
    }
    
    protected function generateId() {
        return uniqid();
    }
    
    protected function find($table, $id) {
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    protected function where($table, $field, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE {$field} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }
    
    protected function all($table) {
        $stmt = $this->db->prepare("SELECT * FROM {$table} ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    protected function save($table, $item) {
        if (isset($item['id']) && !empty($item['id'])) {
            // Update existing record
            return $this->update($table, $item['id'], $item);
        } else {
            // Create new record
            return $this->insert($table, $item);
        }
    }
    
    protected function insert($table, $data) {
        // Remove id from data if it exists
        unset($data['id']);
        
        // Add timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        
        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES ({$placeholders}) RETURNING *";
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetch();
    }
    
    protected function update($table, $id, $data) {
        // Remove id from data
        unset($data['id']);
        
        // Add updated timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $fields = array_keys($data);
        $set = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$table} SET {$set} WHERE id = ? RETURNING *";
        $stmt = $this->db->prepare($sql);
        
        $values = array_values($data);
        $values[] = $id;
        
        $stmt->execute($values);
        return $stmt->fetch();
    }
    
    protected function delete($table, $id) {
        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    protected function count($table) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    protected function search($table, $fields, $query) {
        $conditions = [];
        $params = [];
        
        foreach ($fields as $field) {
            $conditions[] = "{$field} ILIKE ?";
            $params[] = "%{$query}%";
        }
        
        $sql = "SELECT * FROM {$table} WHERE " . implode(' OR ', $conditions) . " ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    protected function paginate($table, $page = 1, $perPage = 10, $conditions = [], $params = []) {
        $offset = ($page - 1) * $perPage;
        
        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) as count FROM {$table} {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalCount = $countStmt->fetch()['count'];
        
        // Get data
        $dataSql = "SELECT * FROM {$table} {$whereClause} ORDER BY id DESC LIMIT {$perPage} OFFSET {$offset}";
        $dataStmt = $this->db->prepare($dataSql);
        $dataStmt->execute($params);
        $data = $dataStmt->fetchAll();
        
        return [
            'data' => $data,
            'total' => $totalCount,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($totalCount / $perPage)
        ];
    }
    
    protected function sum($table, $field, $conditions = [], $params = []) {
        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }
        
        $sql = "SELECT SUM({$field}) as total FROM {$table} {$whereClause}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    protected function recent($table, $limit = 10) {
        $stmt = $this->db->prepare("SELECT * FROM {$table} ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    // Transaction support
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    public function commit() {
        return $this->db->commit();
    }
    
    public function rollback() {
        return $this->db->rollback();
    }
    
    // Raw query execution
    protected function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    protected function execute($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
?>