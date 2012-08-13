<?php
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
    $this->Cell(0,10,utf8_decode('Inspection TIV - Club Aqua Sénart - Page '.$this->PageNo().'/{nb}'),0,0,'C');
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
    $this->Cell(25,8,utf8_decode("Capacité : ".$bloc["capacite"]." litres - Pression service : ".$bloc["pression_service"]." bars - ".
                                "Pression épreuve : ".$bloc["pression_epreuve"]." bars"), 0, 1);
    $this->Cell(25,8,utf8_decode("Première épreuve : ".$bloc["date_premiere_epreuve"]), 0, 0);
    $this->Cell(32);
    $this->Cell(25,8,utf8_decode("Dernière épreuve : ".$bloc["date_derniere_epreuve"]), 0, 0);
    $this->Cell(32);
    $prochaine_epreuve = date("Y-m-d", strtotime("+5 years", strtotime($bloc["date_derniere_epreuve"])));
    $this->Cell(25,8,utf8_decode("Prochaine épreuve : ".$prochaine_epreuve), 0, 1);
  }
  function addAspectInformation($id_inspection, $element) {
    $db_query = "SELECT id, etat_$element, remarque_$element ".
            "FROM inspection_tiv ".
            "WHERE id ='$id_inspection'";
    $db_result = $this->_db_con->query($db_query);
    $inspection = $db_result->fetch_array();
    $this->SetFont('Times', 'BU', 12);
    $this->Ln(8);
    $this->Cell(30, 8, utf8_decode("Etat extérieur :"), 0, 0);
    $this->SetFont('Times', 'B', 12);
    $this->Cell(10, 8, utf8_decode("Bon"), 0, 0);
    $this->Cell(5, 5, "", 1, 0);
    $this->Cell(5);
    $this->Cell(18, 8, utf8_decode("A suivre"), 0, 0);
    $this->Cell(5, 5, "", 1, 0);
    $this->Cell(5);
    $this->Cell(18, 8, utf8_decode("Mauvais"), 0, 0);
    $this->Cell(5, 5, "", 1, 1);
    $this->Cell(0,8,utf8_decode("Commentaire et action si état autre que bon :"), 0, 1);
    $this->Cell(0, 20, "", 1, 1);
  }
}

?>