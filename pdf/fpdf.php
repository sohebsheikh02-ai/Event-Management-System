<?php
/**
 * FPDF 1.86 - Minimal working implementation for EventHub invoices
 * Official library: http://www.fpdf.org
 * This is a simplified version that supports invoice generation
 */

class FPDF {
    public $version = '1.86';
    protected $orientation;
    protected $unit;
    protected $format;
    protected $w;
    protected $h;
    protected $x;
    protected $y;
    protected $lMargin;
    protected $tMargin;
    protected $rMargin;
    protected $bMargin;
    protected $cMargin;
    protected $fontFamily;
    protected $fontSize;
    protected $textColor;
    protected $fillColor;
    protected $drawColor;
    protected $lineWidth;
    protected $page = 0;
    protected $n = 2;
    protected $pages = array();
    protected $offsets = array();
    protected $state = 0;
    protected $buffer = '';
    protected $currentFont = array();
    protected $autoPageBreak;
    protected $pageBreakTrigger;
    protected $k;

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
        $this->orientation = strtoupper($orientation[0]);
        $this->unit = strtolower($unit[0]);
        $this->format = $size;
        $this->k = ($this->unit == 'pt') ? 1 : 2.834645669291339;
        $this->w = 210;
        $this->h = 297;
        $this->lMargin = 10;
        $this->tMargin = 10;
        $this->rMargin = 10;
        $this->bMargin = 10;
        $this->cMargin = 0;
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
        $this->lineWidth = 0.2;
        $this->fontFamily = 'Arial';
        $this->fontSize = 12;
        $this->textColor = '0 0 0';
        $this->fillColor = '255 255 255';
        $this->drawColor = '0 0 0';
        $this->autoPageBreak = true;
        $this->pageBreakTrigger = $this->h - $this->bMargin;
    }

    public function AddPage($orientation = '') {
        if ($this->state == 0) {
            $this->state = 1;
        }
        $this->page++;
        $this->pages[$this->page] = '';
        $this->state = 2;
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
    }

    public function SetFont($family, $style = '', $size = 0) {
        if ($size == 0) $size = $this->fontSize;
        $this->fontFamily = $family;
        $this->fontSize = $size;
        $this->currentFont = array('name' => $family, 'style' => $style, 'size' => $size);
    }

    public function SetFillColor($r, $g = -1, $b = -1) {
        if ($g == -1) {
            $this->fillColor = round($r / 255, 3) . ' ' . round($r / 255, 3) . ' ' . round($r / 255, 3);
        } else {
            $this->fillColor = round($r / 255, 3) . ' ' . round($g / 255, 3) . ' ' . round($b / 255, 3);
        }
    }

    public function SetDrawColor($r, $g = -1, $b = -1) {
        if ($g == -1) {
            $this->drawColor = round($r / 255, 3) . ' ' . round($r / 255, 3) . ' ' . round($r / 255, 3);
        } else {
            $this->drawColor = round($r / 255, 3) . ' ' . round($g / 255, 3) . ' ' . round($b / 255, 3);
        }
    }

    public function SetTextColor($r, $g = -1, $b = -1) {
        if ($g == -1) {
            $this->textColor = round($r / 255, 3) . ' ' . round($r / 255, 3) . ' ' . round($r / 255, 3);
        } else {
            $this->textColor = round($r / 255, 3) . ' ' . round($g / 255, 3) . ' ' . round($b / 255, 3);
        }
    }

    public function Rect($x, $y, $w, $h, $style = '') {
        $op = 'S';
        if ($style == 'F') $op = 'f';
        elseif ($style == 'FD' || $style == 'DF') $op = 'B';
        
        $x = $x * $this->k;
        $y = ($this->h - $y - $h) * $this->k;
        $w = $w * $this->k;
        $h = $h * $this->k;
        $this->_out(sprintf('%.2f %.2f %.2f %.2f re %s', $x, $y, $w, $h, $op));
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
        if ($h == 0) $h = $this->fontSize * 1.25 / $this->k;
        
        if ($fill) {
            $this->_out($this->fillColor . ' rg');
            $this->Rect($this->x, $this->y, $w, $h, 'F');
        }
        
        if (!empty($txt)) {
            if ($this->textColor != '0 0 0') {
                $this->_out($this->textColor . ' rg');
            }
            
            $x = ($this->x + 0.5) * $this->k;
            $y = ($this->h - $this->y - 0.5) * $this->k;
            
            $txt_esc = addcslashes($txt, '()\\');
            $this->_out(sprintf('BT /F1 %.2f Tf %.2f %.2f Td (%s) Tj ET', $this->fontSize, $x, $y, $txt_esc));
        }
        
        if ($ln == 0) {
            $this->x += $w;
        } else {
            $this->x = $this->lMargin;
            $this->y += $h;
        }
    }

    public function MultiCell($w, $h, $txt = '', $border = 0, $align = 'J', $fill = false) {
        $lines = explode("\n", $txt);
        foreach ($lines as $line) {
            $this->Cell($w, $h, $line, $border, 1, $align, $fill);
        }
    }

    public function SetXY($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    public function SetX($x) {
        $this->x = $x;
    }

    public function SetY($y) {
        $this->y = $y;
        $this->x = $this->lMargin;
    }

    public function GetX() {
        return $this->x;
    }

    public function GetY() {
        return $this->y;
    }

    public function Ln($h = null) {
        $this->x = $this->lMargin;
        if (is_null($h)) {
            $this->y += $this->fontSize * 1.25 / $this->k;
        } else {
            $this->y += $h;
        }
    }

    public function setAutoPageBreak($auto, $margin = 0) {
        $this->autoPageBreak = $auto;
        $this->bMargin = $margin;
        $this->pageBreakTrigger = $this->h - $margin;
    }

    public function Output($dest = '', $name = '') {
        if (empty($name) && $dest != '') {
            $name = $dest;
            $dest = 'I';
        }
        if (empty($name)) $name = 'doc.pdf';
        if ($dest == '') $dest = 'I';
        
        // Finalize PDF
        if ($this->state < 3) {
            $this->_out('ET');
        }
        
        $pdf = $this->_buildDocument();
        
        switch ($dest) {
            case 'I':
                header('Content-Type: application/pdf');
                echo $pdf;
                break;
            case 'D':
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $name . '"');
                echo $pdf;
                exit;
            case 'F':
                file_put_contents($name, $pdf);
                break;
            case 'S':
                return $pdf;
        }
        exit;
    }

    private function _buildDocument() {
        // Simple PDF document builder
        $pdf = "%PDF-1.3\n";
        $pdf .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $pdf .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        
        $content = "";
        for ($i = 1; $i <= $this->page; $i++) {
            $content .= $this->pages[$i];
        }
        
        $pdf .= "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595.28 841.89] /Contents 4 0 R >>\nendobj\n";
        $pdf .= "4 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream\nendobj\n";
        $pdf .= "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        
        $xref_pos = strlen($pdf);
        $pdf .= "xref\n";
        $pdf .= "0 6\n";
        $pdf .= "0000000000 65535 f \n";
        $pdf .= str_pad($xref_pos - strlen($pdf) + 15, 10, "0", STR_PAD_LEFT) . " 00000 n \n";
        
        $pdf .= "trailer\n";
        $pdf .= "<< /Size 6 /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xref_pos . "\n";
        $pdf .= "%%EOF";
        
        return $pdf;
    }

    protected function _out($s) {
        if ($this->state == 2) {
            $this->pages[$this->page] .= $s . "\n";
        } else {
            $this->buffer .= $s . "\n";
        }
    }
}
?>
