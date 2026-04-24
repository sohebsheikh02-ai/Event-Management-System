<?php
/**
 * FPDF - PDF document generator
 * Complete working implementation for invoice generation
 */

class FPDF {
    protected $page = 0;
    protected $n = 0;
    protected $offsets = array();
    protected $pages = array();
    protected $state = 0;
    protected $k = 2.834645669;
    protected $w = 210;
    protected $h = 297;
    protected $x = 10;
    protected $y = 10;
    protected $FontSize = 12;
    protected $FontSizePt = 12;
    protected $CurrentFont = array();
    protected $buffer = '';
    protected $objects = array();
    protected $lMargin = 10;
    protected $rMargin = 10;
    protected $tMargin = 10;
    protected $bMargin = 10;
    protected $FillColor = '';
    protected $DrawColor = '';

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
        if ($orientation == 'L') {
            $tmp = $this->w;
            $this->w = $this->h;
            $this->h = $tmp;
        }
        $this->objects = array();
        $this->state = 0;
        $this->page = 0;
        $this->n = 0;
        $this->offsets = array();
        $this->pages = array();
        $this->FillColor = '0 g';
        $this->DrawColor = '0 G';
    }

    public function AddPage() {
        if ($this->state == 0) {
            $this->_newobj();
            $this->state = 3;
        }
        $this->page++;
        $this->pages[$this->page] = '';
        $this->state = 2;
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
    }

    public function SetFont($family, $style = '', $size = 0) {
        if (!$size) $size = 12;
        $this->FontSizePt = $size;
        $this->FontSize = $size / $this->k;
    }

    public function SetFillColor($r, $g = -1, $b = -1) {
        if ($g == -1) {
            $this->FillColor = sprintf('%.3f g', $r / 255);
        } else {
            $this->FillColor = sprintf('%.3f %.3f %.3f rg', $r / 255, $g / 255, $b / 255);
        }
        $this->_out($this->FillColor);
    }

    public function SetDrawColor($r, $g = -1, $b = -1) {
        if ($g == -1) {
            $this->DrawColor = sprintf('%.3f G', $r / 255);
        } else {
            $this->DrawColor = sprintf('%.3f %.3f %.3f RG', $r / 255, $g / 255, $b / 255);
        }
        $this->_out($this->DrawColor);
    }

    public function SetTextColor($r, $g = -1, $b = -1) {
        if ($g == -1) {
            $this->_out(sprintf('%.3f g', $r / 255));
        } else {
            $this->_out(sprintf('%.3f %.3f %.3f rg', $r / 255, $g / 255, $b / 255));
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
        $this->x = $this->lMargin;
        $this->y = $y;
    }

    public function SetMargins($left, $top, $right = null) {
        $this->lMargin = $left;
        $this->tMargin = $top;
        $this->rMargin = $right === null ? $left : $right;
        $this->x = $this->lMargin;
        $this->y = max($this->y, $this->tMargin);
    }

    public function SetAutoPageBreak($auto, $margin = 0) {
        $this->bMargin = $margin;
    }

    public function GetPageWidth() {
        return $this->w;
    }

    public function GetPageHeight() {
        return $this->h;
    }

    public function GetX() {
        return $this->x;
    }

    public function GetY() {
        return $this->y;
    }

    public function Ln($h = 0) {
        $this->x = $this->lMargin;
        $this->y += ($h ? $h : 5);
    }

    public function Rect($x, $y, $w, $h, $style = '') {
        $k = $this->k;
        $op = ($style == 'F') ? 'f' : (($style == 'FD' || $style == 'DF') ? 'B' : 'S');
        $this->_out(sprintf('%.2f %.2f %.2f %.2f re %s', $x * $k, (297 - $y) * $k, $w * $k, -$h * $k, $op));
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false) {
        if ($h == 0) $h = 5;
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        
        if ($fill) {
            $this->_out($this->FillColor);
            $this->Rect($this->x, $this->y, $w, $h, 'F');
        }

        if ($border) {
            $this->_out($this->DrawColor);
            $this->Rect($this->x, $this->y, $w, $h, 'S');
        }

        if ($txt) {
            $k = $this->k;
            $x = $this->x;
            $y = $this->y;
            $padding = 1.5;
            $textWidth = $this->_getTextWidth($txt);

            if ($align == 'R') {
                $x = $this->x + max($padding, $w - $textWidth - $padding);
            } elseif ($align == 'C') {
                $x = $this->x + max($padding, ($w - $textWidth) / 2);
            } else {
                $x = $this->x + $padding;
            }

            $this->_out(sprintf('BT /F1 %.2f Tf %.2f %.2f Td (%s) Tj ET',
                $this->FontSizePt, $x * $k, (297 - $y - 1) * $k, $this->_escape($txt)));
        }

        if ($ln) {
            $this->y += $h;
            if ($ln == 1) $this->x = $this->lMargin;
            else $this->x += $w;
        } else {
            $this->x += $w;
        }
    }

    public function MultiCell($w, $h, $txt = '', $border = 0, $align = '', $fill = false) {
        $lines = explode("\n", $txt);
        foreach ($lines as $line) {
            $this->Cell($w, $h, $line, $border, 1, $align, $fill);
        }
    }

    public function Output($dest = '', $name = '') {
        if (is_string($dest)) {
            $d = strtoupper($dest);
        } else {
            $name = $dest;
            $d = 'D';
        }
        
        if ($this->page === 0) {
            $this->AddPage();
        }
        
        $pdf = $this->_render();
        
        if ($d == 'I') {
            header('Content-Type: application/pdf');
            header('Content-Length: ' . strlen($pdf));
            echo $pdf;
        } elseif ($d == 'D') {
            if (!$name) $name = 'doc.pdf';
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header('Content-Length: ' . strlen($pdf));
            echo $pdf;
        } elseif ($d == 'F') {
            file_put_contents($name, $pdf);
        } elseif ($d == 'S') {
            return $pdf;
        }
        exit;
    }

    protected function _render() {
        $pdf = "%PDF-1.3\n";
        $offsetpos = array();

        $nb = $this->page;
        $fontObjId = 3 + (2 * $nb);

        $offsetpos[1] = strlen($pdf);
        $pdf .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";

        $offsetpos[2] = strlen($pdf);
        $pdf .= "2 0 obj\n<< /Type /Pages /Kids [";
        for ($i = 1; $i <= $nb; $i++) {
            $pdf .= (3 + 2 * ($i - 1)) . " 0 R ";
        }
        $pdf .= "] /Count " . $nb . " >>\nendobj\n";

        for ($i = 1; $i <= $nb; $i++) {
            $pageObjId = 3 + 2 * ($i - 1);
            $contentObjId = $pageObjId + 1;

            $offsetpos[$pageObjId] = strlen($pdf);
            $pdf .= $pageObjId . " 0 obj\n";
            $pdf .= "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595.27 841.89] /Resources << /Font << /F1 " . $fontObjId . " 0 R >> >> /Contents " . $contentObjId . " 0 R >>\n";
            $pdf .= "endobj\n";

            $offsetpos[$contentObjId] = strlen($pdf);
            $content = $this->pages[$i];
            $pdf .= $contentObjId . " 0 obj\n";
            $pdf .= "<< /Length " . strlen($content) . " >>\n";
            $pdf .= "stream\n" . $content . "\nendstream\n";
            $pdf .= "endobj\n";
        }

        $offsetpos[$fontObjId] = strlen($pdf);
        $pdf .= $fontObjId . " 0 obj\n";
        $pdf .= "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\n";
        $pdf .= "endobj\n";

        $offsetxref = strlen($pdf);
        $pdf .= "xref\n";
        $pdf .= "0 " . ($fontObjId + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $fontObjId; $i++) {
            $pdf .= str_pad($offsetpos[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer\n";
        $pdf .= "<< /Size " . ($fontObjId + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $offsetxref . "\n";
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
    
    protected function _escape($s) {
        return str_replace(array('\\', '(', ')'), array('\\\\', '\\(', '\\)'), $s);
    }

    protected function _getTextWidth($s) {
        return strlen($s) * $this->FontSizePt * 0.19;
    }
    
    protected function _newobj() {
        $this->n++;
        $this->offsets[$this->n] = strlen($this->buffer);
    }
}
?>
