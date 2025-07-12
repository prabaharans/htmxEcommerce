<?php

class Order extends Model {
    
    public function create($data) {
        $order = [
            'id' => $this->generateId(),
            'customer' => $data['customer'],
            'items' => $data['items'],
            'total' => $data['total'],
            'payment_id' => $data['payment_id'],
            'status' => $data['status'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->save('orders', $order);
    }
    
    public function getAll() {
        return $this->storage->get('orders', []);
    }
    
    public function getById($id) {
        return $this->find('orders', $id);
    }
    
    public function getRecent($limit = 10) {
        $orders = $this->getAll();
        usort($orders, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        return array_slice($orders, 0, $limit);
    }
    
    public function getCount() {
        return count($this->getAll());
    }
    
    public function getTotalRevenue() {
        $orders = $this->getAll();
        return array_sum(array_column($orders, 'total'));
    }
    
    public function updateStatus($id, $status) {
        $order = $this->getById($id);
        if ($order) {
            $order['status'] = $status;
            $order['updated_at'] = date('Y-m-d H:i:s');
            $this->save('orders', $order);
        }
    }
}
?>
