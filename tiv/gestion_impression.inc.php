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
    $this->SetFont('Arial','B',8);
    $this->Cell(10);
    $this->Cell(180, 10, utf8_decode('Fiche TIV du '.$this->_date.' - club Aqua Sénart'), 1, 0, 'C');
    // Saut de ligne
    $this->Ln(11);
  }

  function Footer() {
    // Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    // Police Arial italique 8
    $this->SetFont('Arial','I',8);
    // Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
  }
}

?>