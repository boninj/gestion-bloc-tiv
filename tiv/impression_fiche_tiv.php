<?php
$date_tiv = $_GET["date"];
require_once("gestion_impression.inc.php");
require_once("connect_db.inc.php");

$pdf = new PdfTIV($date_tiv);
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

$db_query = "SELECT inspection_tiv.id, bloc.id, bloc.numero, bloc.constructeur, bloc.marque, bloc.capacite, ".
            "inspecteur_tiv.nom, bloc.date_derniere_epreuve, bloc.date_premiere_epreuve, bloc.pression_service, bloc.pression_epreuve ".
            "FROM inspection_tiv, bloc, inspecteur_tiv ".
            "WHERE inspection_tiv.date = '$date_tiv' AND id_bloc = bloc.id AND id_inspecteur_tiv = inspecteur_tiv.id ".
            "ORDER BY inspecteur_tiv.nom";
$db_result = $db_con->query($db_query);
while($result = $db_result->fetch_array()) {
  $pdf->AddPage();
  $pdf->SetFont('Times', 'B', 12);
  $pdf->Cell(30,10,utf8_decode("Bloc n° club "), 0, 0);
  $pdf->SetFont('Times',  '', 10);
  $pdf->Cell(8,8,$result[1], 1, 0);
  $pdf->Cell(5);
  $pdf->SetFont('Times', 'B', 12);
  $pdf->Cell(50,10,utf8_decode("Numéro du constructeur"), 0, 0);
  $pdf->SetFont('Times',  '', 10);
  $pdf->Cell(25,8,$result[2], 1, 1);
  $pdf->SetFont('Times',  '', 12);
  $pdf->Cell(25,8,utf8_decode("Capacité : ".$result[5]." litres - Pression service : ".$result[9]." bars - Pression épreuve : ".$result[10]." bars"), 0, 1);
  $pdf->Cell(25,8,utf8_decode("Première épreuve : ".$result[8]), 0, 0);
  $pdf->Cell(32);
  $pdf->Cell(25,8,utf8_decode("Dernière épreuve : ".$result[7]), 0, 0);
  $pdf->Cell(32);
  $prochaine_epreuve = date("Y-m-d", strtotime("+5 years", strtotime($result[7])));
  $pdf->Cell(25,8,utf8_decode("Prochaine épreuve : ".$prochaine_epreuve), 0, 1);
}

$pdf->Output();

?>