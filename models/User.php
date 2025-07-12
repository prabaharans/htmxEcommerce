<?php

class User extends Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function create($data) {
        $user = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'customer'
        ];
        
        return $this->insert('users', $user);
    }
    
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function getById($id) {
        return $this->find('users', $id);
    }
    
    public function getAll() {
        return $this->all('users');
    }
    
    public function updateUser($id, $data) {
        // Don't update password if not provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        
        return $this->update('users', $id, $data);
    }
    
    public function delete($collection, $id) {
        return parent::delete('users', $id);
    }
    
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public function authenticate($email, $password) {
        $user = $this->getByEmail($email);
        
        if ($user && $this->verifyPassword($password, $user['password'])) {
            // Remove password from returned user data
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    public function isAdmin($userId) {
        $user = $this->getById($userId);
        return $user && $user['role'] === 'admin';
    }
    
    public function getCustomers() {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->execute(['customer']);
        return $stmt->fetchAll();
    }
    
    public function getAdmins() {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->execute(['admin']);
        return $stmt->fetchAll();
    }
    
    public function updateLastLogin($id) {
        $stmt = $this->db->prepare("UPDATE users SET updated_at = ? WHERE id = ?");
        return $stmt->execute([date('Y-m-d H:i:s'), $id]);
    }
    
    public function emailExists($email, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
        }
        
        return $stmt->fetch() !== false;
    }
    
    public function getCount() {
        return $this->count('users');
    }
    
    public function getCustomerCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE role = ?");
        $stmt->execute(['customer']);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function createAdmin($data) {
        $data['role'] = 'admin';
        return $this->create($data);
    }
    
    public function changePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ?, updated_at = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, date('Y-m-d H:i:s'), $id]);
    }
    
    public function updateProfile($id, $data) {
        // Only allow certain fields to be updated in profile
        $allowedFields = ['name', 'email'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (!empty($updateData)) {
            return $this->update('users', $id, $updateData);
        }
        
        return false;
    }
}
?>