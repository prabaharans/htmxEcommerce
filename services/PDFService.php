<?php

require_once 'https://tcpdf.tcpdf.sourceforge.net/download/tcpdf_6_6_2.zip';

class PDFService {
    
    public function generateInvoice($orderId) {
        $orderModel = new Order();
        $order = $orderModel->getById($orderId);
        
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator(APP_NAME);
        $pdf->SetAuthor(APP_NAME);
        $pdf->SetTitle('Invoice #' . substr($orderId, 0, 8));
        $pdf->SetSubject('Order Invoice');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(TRUE, 20);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 12);
        
        // Company header
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 15, APP_NAME, 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, 'Premium Dropshipping Products', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Email: info@dropshippro.com | Phone: +1 (555) 123-4567', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Invoice title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'INVOICE', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Invoice details
        $pdf->SetFont('helvetica', '', 10);
        $invoiceDate = date('F j, Y', strtotime($order['created_at']));
        
        $pdf->Cell(30, 6, 'Invoice #:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(60, 6, substr($orderId, 0, 8), 0, 0, 'L');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(30, 6, 'Date:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 6, $invoiceDate, 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(30, 6, 'Order ID:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(60, 6, substr($orderId, 0, 12), 0, 0, 'L');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(30, 6, 'Status:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 6, ucfirst($order['status']), 0, 1, 'L');
        $pdf->Ln(10);
        
        // Customer information
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'Bill To:', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, $order['customer']['name'], 0, 1, 'L');
        $pdf->Cell(0, 5, $order['customer']['email'], 0, 1, 'L');
        $pdf->Cell(0, 5, $order['customer']['address'], 0, 1, 'L');
        $pdf->Cell(0, 5, $order['customer']['city'] . ', ' . $order['customer']['postal_code'], 0, 1, 'L');
        $pdf->Cell(0, 5, $order['customer']['country'], 0, 1, 'L');
        $pdf->Ln(10);
        
        // Items table header
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(80, 8, 'Product', 1, 0, 'L', true);
        $pdf->Cell(25, 8, 'Price', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Qty', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Total', 1, 1, 'C', true);
        
        // Items
        $pdf->SetFont('helvetica', '', 9);
        $subtotal = 0;
        
        foreach ($order['items'] as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $subtotal += $itemTotal;
            
            $pdf->Cell(80, 8, $item['product']['name'], 1, 0, 'L');
            $pdf->Cell(25, 8, formatPrice($item['price']), 1, 0, 'C');
            $pdf->Cell(20, 8, $item['quantity'], 1, 0, 'C');
            $pdf->Cell(25, 8, formatPrice($itemTotal), 1, 1, 'C');
        }
        
        // Totals
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 10);
        
        $pdf->Cell(105, 6, '', 0, 0, 'L');
        $pdf->Cell(20, 6, 'Subtotal:', 0, 0, 'R');
        $pdf->Cell(25, 6, formatPrice($subtotal), 0, 1, 'C');
        
        $pdf->Cell(105, 6, '', 0, 0, 'L');
        $pdf->Cell(20, 6, 'Shipping:', 0, 0, 'R');
        $pdf->Cell(25, 6, 'Free', 0, 1, 'C');
        
        $pdf->Cell(105, 6, '', 0, 0, 'L');
        $pdf->Cell(20, 6, 'Tax:', 0, 0, 'R');
        $pdf->Cell(25, 6, formatPrice(0), 0, 1, 'C');
        
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(105, 8, '', 0, 0, 'L');
        $pdf->Cell(20, 8, 'Total:', 1, 0, 'R', true);
        $pdf->Cell(25, 8, formatPrice($order['total']), 1, 1, 'C', true);
        
        // Payment information
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 6, 'Payment Information:', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 5, 'Payment Method: Credit Card (Stripe)', 0, 1, 'L');
        $pdf->Cell(0, 5, 'Payment ID: ' . $order['payment_id'], 0, 1, 'L');
        $pdf->Cell(0, 5, 'Payment Status: Completed', 0, 1, 'L');
        
        // Footer
        $pdf->Ln(15);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 4, 'Thank you for your business!', 0, 1, 'C');
        $pdf->Cell(0, 4, 'For questions about this invoice, contact us at info@dropshippro.com', 0, 1, 'C');
        
        return $pdf->Output('', 'S');
    }
}
?>
