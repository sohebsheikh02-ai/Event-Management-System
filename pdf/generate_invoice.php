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
  SELECT i.*,
         COALESCE(u.name, ru.name) AS user_name,
         COALESCE(u.email, ru.email) AS email,
         COALESCE(u.phone, ru.phone) AS phone,
         COALESCE(e.title, re.title) AS event_title,
         COALESCE(e.event_date, re.event_date) AS event_date,
         COALESCE(e.event_time, re.event_time) AS event_time,
         COALESCE(e.venue, re.venue) AS venue
  FROM invoices i
  LEFT JOIN users u ON u.id = i.user_id
  LEFT JOIN events e ON e.id = i.event_id
  LEFT JOIN registrations r ON r.id = i.registration_id
  LEFT JOIN users ru ON ru.id = r.user_id
  LEFT JOIN events re ON re.id = r.event_id
  WHERE i.id = ?");
$stmt->bind_param('i',$invoice_id); $stmt->execute();
$inv = $stmt->get_result()->fetch_assoc();
if (!$inv) die("Invoice not found");

// Access control: owner or admin
if (!(is_admin() || (is_logged_in() && $_SESSION['user_id'] == $inv['user_id']))) die("Unauthorized");

$invoiceNumber = $inv['invoice_number'] ?: ('INV-' . str_pad((string) $invoice_id, 5, '0', STR_PAD_LEFT));
$invoiceDate = !empty($inv['created_at']) ? date('d M Y', strtotime($inv['created_at'])) : date('d M Y');
$userName = $inv['user_name'] ?: 'Registered User';
$userEmail = $inv['email'] ?: '-';
$userPhone = $inv['phone'] ?: '-';
$eventTitle = $inv['event_title'] ?: 'Event Registration';
$eventDate = !empty($inv['event_date']) ? date('d M Y', strtotime($inv['event_date'])) : '-';
$eventTime = !empty($inv['event_time']) ? date('h:i A', strtotime($inv['event_time'])) : '-';
$venue = $inv['venue'] ?: '-';
$amount = isset($inv['amount']) ? (float) $inv['amount'] : 0.0;
$status = $inv['status'] ?: 'Paid';

$pdf = new FPDF();
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();

$leftMargin = 15;
$topMargin = 15;
$contentWidth = $pdf->GetPageWidth() - 30;
$rightColumnWidth = 58;
$leftColumnWidth = $contentWidth - $rightColumnWidth - 8;
$metaWidth = 84;
$descWidth = 102;
$qtyWidth = 24;
$amountWidth = $contentWidth - $descWidth - $qtyWidth;
$totalsLabelWidth = 38;
$totalsValueWidth = 42;
$totalsWidth = $totalsLabelWidth + $totalsValueWidth;
$totalsX = $leftMargin + $contentWidth - $totalsWidth;

// Header
$pdf->SetFillColor(79,70,229);
$pdf->Rect($leftMargin, $topMargin, $contentWidth, 28, 'F');
$pdf->SetTextColor(255);
$pdf->SetFont('Arial','B',22);
$pdf->SetXY($leftMargin + 6, $topMargin + 5);
$pdf->Cell(80,10,'EventHub',0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->SetXY($leftMargin + 6, $topMargin + 16);
$pdf->Cell(80,6,'Event Management System',0,0,'L');
$pdf->SetFont('Arial','B',16);
$pdf->SetXY($leftMargin + $contentWidth - 50, $topMargin + 8);
$pdf->Cell(44,8,'INVOICE',0,0,'R');

$pdf->SetY($topMargin + 38);
$pdf->SetTextColor(0);

// Invoice meta
$pdf->SetFont('Arial','B',10);
$pdf->SetXY($leftMargin, $topMargin + 38);
$pdf->Cell($metaWidth,6,'Invoice #',0,0,'L');
$pdf->SetXY($leftMargin + $contentWidth - $metaWidth, $topMargin + 38);
$pdf->Cell($metaWidth,6,'Invoice Date',0,0,'L');

$pdf->SetFont('Arial','',10);
$pdf->SetXY($leftMargin, $topMargin + 45);
$pdf->Cell($metaWidth,6,$invoiceNumber,0,0,'L');
$pdf->SetXY($leftMargin + $contentWidth - $metaWidth, $topMargin + 45);
$pdf->Cell($metaWidth,6,$invoiceDate,0,0,'L');

$pdf->SetFillColor(226,232,240);
$pdf->Rect($leftMargin, $topMargin + 55, $contentWidth, 0.4, 'F');
$pdf->SetY($topMargin + 62);

// Bill To & Event Details
$pdf->SetFillColor(247,248,251);
$pdf->SetFont('Arial','B',11);
$pdf->SetX($leftMargin);
$pdf->Cell($leftColumnWidth,8,'Bill To',0,0,'L',true);
$pdf->Cell(8,8,'',0,0);
$pdf->Cell($rightColumnWidth,8,'Event Details',0,1,'L',true);

$pdf->SetFont('Arial','',10);
$y = $pdf->GetY();
$pdf->SetXY($leftMargin, $y);
$pdf->MultiCell($leftColumnWidth,6, $userName."\n".$userEmail."\n".$userPhone, 0);
$leftBottomY = $pdf->GetY();
$pdf->SetXY($leftMargin + $leftColumnWidth + 8, $y);
$pdf->MultiCell($rightColumnWidth,6, $eventTitle."\n".$eventDate.' at '.$eventTime."\n".$venue, 0);
$pdf->SetY(max($leftBottomY, $pdf->GetY()) + 10);

// Line items table
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(79,70,229);
$pdf->SetTextColor(255);
$pdf->SetX($leftMargin);
$pdf->Cell($descWidth,9,'Description',0,0,'L',true);
$pdf->Cell($qtyWidth,9,'Qty',0,0,'C',true);
$pdf->Cell($amountWidth,9,'Amount',0,1,'R',true);

$pdf->SetTextColor(0);
$pdf->SetFont('Arial','',10);
$pdf->SetX($leftMargin);
$pdf->Cell($descWidth,9,'Event Registration: ' . substr($eventTitle,0,42),1,0,'L');
$pdf->Cell($qtyWidth,9,'1',1,0,'C');
$pdf->Cell($amountWidth,9,'INR ' . number_format($amount,2),1,1,'R');

// Totals
$subtotal = $amount;
$tax = round($subtotal * 0.18, 2);
$total = $subtotal + $tax;

$pdf->Ln(8);
$pdf->SetFont('Arial','',10);
$pdf->SetX($totalsX);
$pdf->Cell($totalsLabelWidth,7,'Subtotal',0,0,'L');
$pdf->Cell($totalsValueWidth,7,'INR ' . number_format($subtotal,2),0,1,'R');
$pdf->SetX($totalsX);
$pdf->Cell($totalsLabelWidth,7,'GST (18%)',0,0,'L');
$pdf->Cell($totalsValueWidth,7,'INR ' . number_format($tax,2),0,1,'R');

$pdf->SetFont('Arial','B',12);
$pdf->SetX($totalsX);
$pdf->Cell($totalsLabelWidth,10,'Total',1,0,'L');
$pdf->Cell($totalsValueWidth,10,'INR ' . number_format($total,2),1,1,'R');

// Status
$pdf->Ln(8);
$pdf->SetFont('Arial','B',11);
if ($status==='Paid') $pdf->SetFillColor(209,250,229);
elseif ($status==='Free') $pdf->SetFillColor(219,234,254);
else $pdf->SetFillColor(254,243,199);
$pdf->SetX($leftMargin);
$pdf->Cell(34,10,strtoupper($status),0,1,'C',true);

// Footer note
$pdf->Ln(12);
$pdf->SetFont('Arial','I',9);
$pdf->SetTextColor(107,114,128);
$pdf->SetX($leftMargin);
$pdf->MultiCell($contentWidth,5,"Thank you for registering with EventHub!\nThis is a computer-generated invoice. For queries contact support@eventhub.com",0);

$filename = 'invoice_' . $invoiceNumber . '.pdf';
$pdf->Output('D', $filename);
