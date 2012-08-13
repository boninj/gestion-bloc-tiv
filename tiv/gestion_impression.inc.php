<?php
require_once("definition_element.inc.php");
require_once("connect_db.inc.php");
require_once('fpdf17/fpdf.php');

class PdfTIV extends FPDF {
  var $_date;
  var $_db_con;
  function PdfTIV($date, $db_con) {
    $this->_date = $date;
    $this->_db_con = $db_con;
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
    $this->Cell(0,10,utf8_decode('Inspection TIV du '.$this->_date.' - Club Aqua Sénart - Page '.$this->PageNo().'/{nb}'),0,0,'C');
  }
  function addBlocInformation($id_bloc) {
    $db_query = "SELECT id, id_club, numero, capacite, date_premiere_epreuve, date_derniere_epreuve, pression_service, pression_epreuve ".
                "FROM bloc ".
                "WHERE id ='$id_bloc'";
    $db_result = $this->_db_con->query($db_query);
    $bloc = $db_result->fetch_array();
    $this->SetFont('Times', 'B', 12);
    $this->Cell(30,10,utf8_decode("Bloc n° club "), 0, 0);
    $this->SetFont('Times',  '', 10);
    $this->Cell(8,8,$bloc["id_club"], 1, 0);
    $this->Cell(5);
    $this->SetFont('Times', 'B', 12);
    $this->Cell(50,10,utf8_decode("Numéro du constructeur"), 0, 0);
    $this->SetFont('Times',  '', 10);
    $this->Cell(25,8,$bloc["numero"], 1, 1);
    $this->SetFont('Times',  '', 12);
    $this->Cell(25,8,utf8_decode("Capacité (litres) : ".$bloc["capacite"]." - Pression service : ".$bloc["pression_service"]." bars - ".
                                "Pression épreuve : ".$bloc["pression_epreuve"]." bars"), 0, 1);
    $this->Cell(25,8,utf8_decode("Première épreuve : ".$bloc["date_premiere_epreuve"]), 0, 0);
    $this->Cell(32);
    $this->Cell(25,8,utf8_decode("Dernière épreuve : ".$bloc["date_derniere_epreuve"]), 0, 0);
    $this->Cell(32);
    $prochaine_epreuve = date("Y-m-d", strtotime("+5 years", strtotime($bloc["date_derniere_epreuve"])));
    $this->Cell(25,8,utf8_decode("Prochaine épreuve : ".$prochaine_epreuve), 0, 1);
  }
  function addAspectInformation($id_inspection, $element) {
    $labels = array("interieur" => "intérieur", "exterieur" => "extérieur");
    $label = $element;
    if(array_key_exists($element, $labels)) $label = $labels[$element];

    $db_query = "SELECT id, etat_$element, remarque_$element ".
                "FROM inspection_tiv ".
                "WHERE id ='$id_inspection'";
    $db_result = $this->_db_con->query($db_query);
    $inspection = $db_result->fetch_array();
    $status = inspection_tivElement::getPossibleStatus($element == "interieur");
    $this->SetFont('Times', 'BU', 12);
    $this->Ln(8);
    $this->Cell(33, 8, utf8_decode("État $label :"), 0, 0);
    $this->SetFont('Times', 'B', 12);
    foreach($status as $state) {
      if(strlen($state) == 0) continue;
      $len = $this->GetStringWidth($state) + 2;
      $this->Cell($len, 8, utf8_decode($state), 0, 0);
      $this->Cell(5, 5, ($inspection["etat_$element"] == $state ? "X" : ""), 1, 0);
      $this->Cell(5);
    }
    $this->Cell(5, 7, "", 0, 1);
    $this->Cell(0,8,utf8_decode("Commentaire et action si état autre que bon :"), 0, 1);
    $this->MultiCell(0, 8, ($inspection["remarque_$element"] ? utf8_decode($inspection["remarque_$element"]) : "\n "), 1, 1);
  }
}

?>