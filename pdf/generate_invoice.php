<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Requires FPDF: download from http://www.fpdf.org and place fpdf.php in this folder.
if (!file_exists(__DIR__ . '/fpdf.php')) {
    die("Error: FPDF library not found. Download fpdf.php from http://www.fpdf.org and place it in /pdf/ folder.");
}
require_once __DIR__ . '/fpdf.php';

$invoice_id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("
  SELECT i.*, u.name user_name, u.email, u.phone, e.title event_title, e.event_date, e.event_time, e.venue
  FROM invoices i
  JOIN users u ON u.id = i.user_id
  JOIN events e ON e.id = i.event_id
  WHERE i.id = ?");
$stmt->bind_param('i',$invoice_id); $stmt->execute();
$inv = $stmt->get_result()->fetch_assoc();
if (!$inv) die("Invoice not found");

// Access control: owner or admin
if (!(is_admin() || (is_logged_in() && $_SESSION['user_id'] == $inv['user_id']))) die("Unauthorized");

$pdf = new FPDF();
$pdf->AddPage();

// Header
$pdf->SetFillColor(79,70,229);
$pdf->Rect(0,0,210,30,'F');
$pdf->SetTextColor(255);
$pdf->SetFont('Arial','B',22);
$pdf->SetXY(10,8); $pdf->Cell(0,10,'EventHub',0,1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(10); $pdf->Cell(0,6,'Event Management System',0,1);
$pdf->SetXY(140,10);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(60,8,'INVOICE',0,1,'R');

$pdf->Ln(20);
$pdf->SetTextColor(0);

// Invoice meta
$pdf->SetFont('Arial','',10);
$pdf->Cell(95,6,'Invoice #: ' . $inv['invoice_number'],0,0);
$pdf->Cell(0,6,'Date: ' . date('d M Y', strtotime($inv['created_at'])),0,1,'R');
$pdf->Ln(8);

// Bill To & Event Details
$pdf->SetFillColor(247,248,251);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(95,8,'  Bill To',0,0,'L',true);
$pdf->Cell(95,8,'  Event Details',0,1,'L',true);

$pdf->SetFont('Arial','',10);
$y = $pdf->GetY();
$pdf->SetXY(10,$y);
$pdf->MultiCell(95,6,
  $inv['user_name']."\n".$inv['email']."\n".($inv['phone']?:'-'),0);
$y2 = $pdf->GetY();
$pdf->SetXY(105,$y);
$pdf->MultiCell(95,6,
  $inv['event_title']."\n".date('d M Y',strtotime($inv['event_date'])).' at '.date('h:i A',strtotime($inv['event_time']))."\n".$inv['venue'],0);
$pdf->SetY(max($y2, $pdf->GetY()) + 8);

// Line items table
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(79,70,229); $pdf->SetTextColor(255);
$pdf->Cell(110,9,'  Description',0,0,'L',true);
$pdf->Cell(30,9,'Qty',0,0,'C',true);
$pdf->Cell(50,9,'Amount',0,1,'R',true);

$pdf->SetTextColor(0); $pdf->SetFont('Arial','',10);
$pdf->Cell(110,9,'  Event Registration: ' . substr($inv['event_title'],0,40),'B',0);
$pdf->Cell(30,9,'1','B',0,'C');
$pdf->Cell(50,9,'INR ' . number_format($inv['amount'],2),'B',1,'R');

// Totals
$subtotal = $inv['amount'];
$tax = round($subtotal * 0.18, 2);
$total = $subtotal + $tax;

$pdf->Ln(4);
$pdf->Cell(110,7,'',0,0);
$pdf->Cell(30,7,'Subtotal:',0,0,'R');
$pdf->Cell(50,7,'INR ' . number_format($subtotal,2),0,1,'R');
$pdf->Cell(110,7,'',0,0);
$pdf->Cell(30,7,'GST (18%):',0,0,'R');
$pdf->Cell(50,7,'INR ' . number_format($tax,2),0,1,'R');

$pdf->SetFont('Arial','B',12);
$pdf->Cell(110,10,'',0,0);
$pdf->Cell(30,10,'Total:','T',0,'R');
$pdf->Cell(50,10,'INR ' . number_format($total,2),'T',1,'R');

// Status
$pdf->Ln(6);
$pdf->SetFont('Arial','B',11);
if ($inv['status']==='Paid') $pdf->SetFillColor(209,250,229);
elseif ($inv['status']==='Free') $pdf->SetFillColor(219,234,254);
else $pdf->SetFillColor(254,243,199);
$pdf->Cell(40,10,strtoupper($inv['status']),0,1,'C',true);

// Footer note
$pdf->Ln(10);
$pdf->SetFont('Arial','I',9);
$pdf->SetTextColor(107,114,128);
$pdf->MultiCell(0,5,"Thank you for registering with EventHub!\nThis is a computer-generated invoice. For queries contact support@eventhub.com",0);

$filename = 'invoice_' . $inv['invoice_number'] . '.pdf';
$pdf->Output('D', $filename);
