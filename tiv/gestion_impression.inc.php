<?php
require_once("connect_db.inc.php");
require_once('fpdf17/fpdf.php');

class PdfTIV extends FPDF {
  var $_date;
  function PdfTIV($date) {
    $this->_date = $date;
    parent::__construct();
  }
  function Header() {
    $this->Image('logo_club.png', 10, 6, 10);
    $this->SetFont('Arial','B',10);
    $this->Cell(10);
    $this->Cell(180, 8, utf8_decode('Fiche TIV du '.$this->_date.' - club Aqua Sénart'), 'B', 0, 'C');
    $this->Ln(11);
  }
  function Footer() {
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,utf8_decode('Inspection TIV - Club Aqua Sénart - Page '.$this->PageNo().'/{nb}'),0,0,'C');
  }
}

?>