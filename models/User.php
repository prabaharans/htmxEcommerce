<?php

class User extends Model {
    
    public function create($data) {
        $user = [
            'id' => $this->generateId(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'customer',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->save('users', $user);
    }
    
    public function getByEmail($email) {
        return $this->where('users', 'email', $email)[0] ?? null;
    }
    
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>
