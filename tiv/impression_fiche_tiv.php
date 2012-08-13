<?php
$date_tiv = $_GET["date"];
require_once("gestion_impression.inc.php");
require_once("connect_db.inc.php");

$pdf = new PdfTIV($date_tiv, $db_con);
$pdf->AliasNbPages();
/*$pdf->AddPage();
$pdf->SetFont('Times','B',16);

$pdf->Cell(0,10,utf8_decode("Informations relatives à l'inspection TIV du $date_tiv"),0,1);

$pdf->SetFont('Times','B',12);

$db_query = "SELECT inspection_tiv.id, bloc.date_derniere_epreuve FROM inspection_tiv,bloc ".
            "WHERE date = '$date_tiv' AND bloc.id = inspection_tiv.id_bloc" ;
$db_result = $db_con->query($db_query);
$total = 0;
$count_tiv = 0;
$reepreuve = 0;
$max_time_tiv = strtotime("-48 months", strtotime($date_tiv));
while($result = $db_result->fetch_array()) {
  $total++;
  $time = strtotime($result[1]);
  if($time > $max_time_tiv) $count_tiv++;
  else $reepreuve++;
}
$pdf->Cell(0,10,utf8_decode("Il est prévu d'inspecter $total blocs au total dont $reepreuve réépreuve(s) et ".$count_tiv." inspection(s) TIV."), 0, 1);
$pdf->Cell(0,10,utf8_decode("Vous trouverez l'ensemble des fiches TIV dans les pages suivantes."), 0, 1);*/

$db_query = "SELECT inspection_tiv.id, id_bloc, inspecteur_tiv.numero_tiv, decision, inspecteur_tiv.nom ".
            "FROM inspection_tiv, inspecteur_tiv ".
            "WHERE inspection_tiv.date = '$date_tiv' AND id_inspecteur_tiv = inspecteur_tiv.id ".
            "ORDER BY inspecteur_tiv.id DESC";
$db_result = $db_con->query($db_query);
while($result = $db_result->fetch_array()) {
  // Affichage de l'entête de la fiche (capacité du bloc, date des réépreuves etc.)
  $pdf->AddPage();
  $pdf->addBlocInformation($result[1]);
  // Ligne de séparation
  $pdf->Cell(0,5,"", 'B', 1, 1);
  $pdf->Ln(8);
  // Information concernant l'inspection TIV
  $pdf->SetFont('Times', 'B', 14);
  $pdf->Cell(45,10,utf8_decode("Vérificateur TIV n° "), 0, 0);
  $pdf->SetFont('Times',  '', 12);
  $pdf->Cell($pdf->GetStringWidth($result[2]) + 2,8,$result[2], 1, 0);
  $pdf->Cell(3);
  // Affichage numéro fiche tiv
  $pdf->SetFont('Times', 'B', 14);
  $pdf->Cell(30,10,utf8_decode("Fiche TIV n° "), 0, 0);
  $pdf->SetFont('Times',  '', 12);
  $pdf->Cell($pdf->GetStringWidth($result[0]) + 2,8,$result[0], 1, 1);
  foreach(array("exterieur", "interieur", "filetage", "robineterie") as $element)
    $pdf->addAspectInformation($result[0], $element);
  // Ligne de séparation
  $pdf->Cell(0,2,"", 'B', 1, 1);
  // Conclusion + signature
  $pdf->Ln(1);
  $pdf->SetFont('Times',  'B', 12);
  $pdf->Cell(30,8,"Conclusions : ", 0, 0);
  $pdf->Cell(17,10,utf8_decode($result[3]), 1, 0);
  $pdf->Cell(10);
  $pdf->Cell(30,8,"Signatures : ", 0, 0);
  $pdf->MultiCell(60,10,utf8_decode($result[4])."\n\n ", 1, 'C');
}

$pdf->Output();

?>