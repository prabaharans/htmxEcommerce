<?php

class Order extends Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function create($orderData) {
        try {
            $this->beginTransaction();
            
            // Generate unique order number
            $orderNumber = $this->generateOrderNumber();
            
            // Prepare order data
            $order = [
                'order_number' => $orderNumber,
                'customer_name' => $orderData['customer']['name'],
                'customer_email' => $orderData['customer']['email'],
                'customer_phone' => $orderData['customer']['phone'] ?? null,
                'customer_address' => $orderData['customer']['address'],
                'customer_city' => $orderData['customer']['city'],
                'customer_postal_code' => $orderData['customer']['postal_code'],
                'customer_country' => $orderData['customer']['country'],
                'subtotal' => $orderData['subtotal'],
                'tax' => $orderData['tax'] ?? 0,
                'shipping' => $orderData['shipping'] ?? 0,
                'total' => $orderData['total'],
                'status' => 'pending',
                'payment_id' => $orderData['payment_id'] ?? null,
                'payment_status' => $orderData['payment_status'] ?? 'pending',
                'notes' => $orderData['notes'] ?? null
            ];
            
            // Insert order
            $createdOrder = $this->insert('orders', $order);
            
            // Insert order items
            if (isset($orderData['items']) && !empty($orderData['items'])) {
                foreach ($orderData['items'] as $item) {
                    $orderItem = [
                        'order_id' => $createdOrder['id'],
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product']['name'],
                        'product_image' => $item['product']['image'] ?? null,
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total' => $item['price'] * $item['quantity']
                    ];
                    
                    $this->insert('order_items', $orderItem);
                    
                    // Update product stock
                    $productModel = new Product();
                    $productModel->updateStock($item['product_id'], $item['quantity']);
                }
            }
            
            $this->commit();
            
            // Return order with items
            return $this->getById($createdOrder['id']);
            
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    public function getById($id) {
        $order = $this->find('orders', $id);
        if ($order) {
            $order['items'] = $this->getOrderItems($id);
        }
        return $order;
    }
    
    public function getAll() {
        return $this->all('orders');
    }
    
    public function getRecent($limit = 10) {
        return $this->recent('orders', $limit);
    }
    
    public function getCount() {
        return $this->count('orders');
    }
    
    public function getTotalRevenue() {
        return $this->sum('orders', 'total', ['payment_status = ?'], ['completed']);
    }
    
    public function getOrderItems($orderId) {
        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ? ORDER BY id");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    
    public function updateStatus($id, $status) {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $this->update('orders', $id, $data);
    }
    
    public function updatePaymentStatus($id, $paymentStatus, $paymentId = null) {
        $data = [
            'payment_status' => $paymentStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($paymentId) {
            $data['payment_id'] = $paymentId;
        }
        
        return $this->update('orders', $id, $data);
    }
    
    public function updateSupplierInfo($id, $supplierOrderId, $trackingNumber = null) {
        $data = [
            'supplier_order_id' => $supplierOrderId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($trackingNumber) {
            $data['tracking_number'] = $trackingNumber;
        }
        
        return $this->update('orders', $id, $data);
    }
    
    public function getByOrderNumber($orderNumber) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_number = ?");
        $stmt->execute([$orderNumber]);
        $order = $stmt->fetch();
        
        if ($order) {
            $order['items'] = $this->getOrderItems($order['id']);
        }
        
        return $order;
    }
    
    public function getByStatus($status) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
    
    public function getByDateRange($startDate, $endDate) {
        $stmt = $this->db->prepare("
            SELECT * FROM orders 
            WHERE created_at >= ? AND created_at <= ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    public function getPaginated($page = 1, $perPage = 20, $status = null) {
        $conditions = [];
        $params = [];
        
        if ($status) {
            $conditions[] = 'status = ?';
            $params[] = $status;
        }
        
        return $this->paginate('orders', $page, $perPage, $conditions, $params);
    }
    
    public function getRevenueByMonth($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                EXTRACT(MONTH FROM created_at) as month,
                SUM(total) as revenue,
                COUNT(*) as orders
            FROM orders 
            WHERE EXTRACT(YEAR FROM created_at) = ? 
            AND payment_status = 'completed'
            GROUP BY EXTRACT(MONTH FROM created_at)
            ORDER BY month
        ");
        $stmt->execute([$year]);
        return $stmt->fetchAll();
    }
    
    public function getTopProducts($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT 
                oi.product_name,
                SUM(oi.quantity) as total_sold,
                SUM(oi.total) as total_revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.payment_status = 'completed'
            GROUP BY oi.product_id, oi.product_name
            ORDER BY total_sold DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    private function generateOrderNumber() {
        $prefix = 'ORD';
        $timestamp = date('Ymd');
        
        // Get the last order number for today
        $stmt = $this->db->prepare("
            SELECT order_number FROM orders 
            WHERE order_number LIKE ? 
            ORDER BY order_number DESC 
            LIMIT 1
        ");
        $stmt->execute([$prefix . $timestamp . '%']);
        $lastOrder = $stmt->fetch();
        
        if ($lastOrder) {
            // Extract the sequence number and increment
            $lastSequence = (int)substr($lastOrder['order_number'], -4);
            $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // First order of the day
            $sequence = '0001';
        }
        
        return $prefix . $timestamp . $sequence;
    }
    
    public function delete($collection, $id) {
        // Also delete order items (handled by CASCADE in database)
        return parent::delete('orders', $id);
    }
    
    public function getCustomerOrders($email) {
        $stmt = $this->db->prepare("
            SELECT * FROM orders 
            WHERE customer_email = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$email]);
        return $stmt->fetchAll();
    }
}
?>