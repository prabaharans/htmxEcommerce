<?php

class OrderController extends Controller {
    
    public function invoice($orderId) {
        $orderModel = new Order();
        $order = $orderModel->getById($orderId);
        
        if (!$order) {
            http_response_code(404);
            echo "Order not found";
            return;
        }
        
        // Generate and serve PDF invoice
        $pdfService = new PDFService();
        $pdfContent = $pdfService->generateInvoice($orderId);
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="invoice_' . $orderId . '.pdf"');
        echo $pdfContent;
    }
}
?>
