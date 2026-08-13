<?php
session_start();
include "../include/connect_db.php";
require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    public function Header() {
        $image_file = '../images/food_logo.png';
        $this->Image($image_file, 15, 10, 25, 25, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        
        // Header Details
        $this->SetFont('helvetica', 'B', 12);
        $this->SetXY(150, 10);
        $this->Cell(50, 5, 'Food Pilferage Management', 0, 1, 'R');
        $this->SetFont('helvetica', '', 9);
        $this->SetXY(150, 15);
        $this->Cell(50, 5, 'Isabela State University', 0, 1, 'R');
        $this->SetXY(150, 20);
        $this->Cell(50, 5, 'Contact: 09155679710', 0, 1, 'R');
        $this->SetXY(150, 25);
        $this->Cell(50, 5, 'Email: foodpilferage@gmail.com', 0, 1, 'R');
        
        // Report Title and Reference
        $this->SetY(45);
        $this->SetFont('helvetica', 'B', 18);
        $this->Cell(0, 15, 'PILFERAGE REPORT', 0, 1, 'C');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 5, 'Reference No: PR-' . date('Ymd-His'), 0, 1, 'C');
        $this->Cell(0, 5, 'Generated on: ' . date('F j, Y h:i A'), 0, 1, 'C');
        
        $this->Line(15, 70, 195, 70);
        $this->Ln(15);
    }

    public function Footer() {
        $this->SetY(-25);
        $this->Line(15, 270, 195, 270);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'This is a system-generated report. For inquiries, please contact the administrator.', 0, 1, 'C');
        $this->Cell(0, 5, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Initialize PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('Food Pilferage Management System');
$pdf->SetAuthor('System Administrator');
$pdf->SetTitle('Pilferage Report ' . date('Y-m-d'));
$pdf->SetMargins(15, 75, 15);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, 30);
$pdf->AddPage();

// Report Overview
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 10, 'REPORT OVERVIEW', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);

// Summary Query
$total_query = "SELECT
    COUNT(*) as total_reports,
    SUM(CASE WHEN report_status_id = 1 THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN report_status_id = 2 THEN 1 ELSE 0 END) as investigating,
    SUM(CASE WHEN report_status_id = 3 THEN 1 ELSE 0 END) as resolved
FROM pilferage_report";
$total_result = mysqli_query($conn, $total_query);
$summary = mysqli_fetch_assoc($total_result);

// Summary Boxes
$pdf->SetFillColor(245, 245, 245);
$pdf->Cell(45, 8, 'Total Reports: ' . $summary['total_reports'], 1, 0, 'L', 1);
$pdf->Cell(45, 8, 'Pending: ' . $summary['pending'], 1, 0, 'L', 1);
$pdf->Cell(45, 8, 'Investigating: ' . $summary['investigating'], 1, 0, 'L', 1);
$pdf->Cell(45, 8, 'Resolved: ' . $summary['resolved'], 1, 1, 'L', 1);
$pdf->Ln(10);

// Detailed Report Table
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 10, 'DETAILED REPORT', 0, 1, 'L');

// Table Headers
$header = array('Report ID', 'Item Name', 'Reported By', 'Quantity', 'Date', 'Status');
$w = array(25, 45, 40, 25, 30, 25);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(40, 167, 69);
$pdf->SetTextColor(255);

foreach($header as $i => $col) {
    $pdf->Cell($w[$i], 7, $col, 1, 0, 'C', 1);
}
$pdf->Ln();

// Table Data
$pdf->SetFillColor(255);
$pdf->SetTextColor(0);
$pdf->SetFont('helvetica', '', 9);

$query = "SELECT 
    pr.report_id,
    i.item_name,
    CONCAT(u.firstname, ' ', u.lastname) as full_name,
    pr.reported_quantity,
    DATE_FORMAT(pr.date_reported, '%M %d, %Y') as formatted_date,
    rs.report_status
FROM pilferage_report pr
JOIN items i ON pr.item_id = i.item_id
JOIN users u ON pr.user_id = u.user_id
JOIN report_status rs ON pr.report_status_id = rs.report_status_id
ORDER BY CAST(pr.report_id AS UNSIGNED) DESC";

$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result)) {
    foreach($w as $i => $width) {
        $value = match($i) {
            0 => $row['report_id'],
            1 => $row['item_name'],
            2 => $row['full_name'],
            3 => $row['reported_quantity'],
            4 => $row['formatted_date'],
            5 => $row['report_status'],
            default => ''
        };
        $align = in_array($i, [0, 3, 4, 5]) ? 'C' : 'L';
        $pdf->Cell($width, 6, $value, 1, 0, $align);
    }
    $pdf->Ln();
}

// Signature Section
$pdf->Ln(20);
$user_id = $_SESSION['user_id'];
$user_query = "SELECT firstname, lastname, role_id FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$role = match($user['role_id']) {
    1 => 'Administrator',
    2 => 'Inventory Staff',
    default => 'Unauthorized Personnel'
};

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(90, 5, 'Prepared by:', 0, 0, 'L');
$pdf->Ln(15);
$pdf->Cell(90, 0, '________________________', 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(90, 5, $user['firstname'] . ' ' . $user['lastname'], 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(90, 5, $role, 0, 0, 'L');

$pdf->Output('pilferage_report_' . date('Y-m-d') . '.pdf', 'D');
