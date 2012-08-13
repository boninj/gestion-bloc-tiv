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

$db_query = "SELECT inspection_tiv.id, bloc.id, bloc.numero, bloc.constructeur, bloc.marque, bloc.capacite, ".
            "inspecteur_tiv.nom, bloc.date_derniere_epreuve, bloc.date_premiere_epreuve, bloc.pression_service, bloc.pression_epreuve, inspecteur_tiv.numero_tiv ".
            "FROM inspection_tiv, bloc, inspecteur_tiv ".
            "WHERE inspection_tiv.date = '$date_tiv' AND id_bloc = bloc.id AND id_inspecteur_tiv = inspecteur_tiv.id ".
            "ORDER BY inspecteur_tiv.nom DESC";
$db_result = $db_con->query($db_query);
while($result = $db_result->fetch_array()) {
  // Affichage de l'entête de la fiche (capacité du bloc, date des réépreuves etc.)
  $pdf->AddPage();
  $pdf->addBlocInformation($result[1]);
  // Ligne de séparation
  $pdf->Cell(0,5,"", 'B', 1, 1);
  // Information concernant l'inspection TIV
  $pdf->SetFont('Times', 'B', 14);
  $pdf->Cell(50,10,utf8_decode("Vérificateur TIV n° "), 0, 0);
  $pdf->SetFont('Times',  '', 12);
  $pdf->Cell(17,8,$result[11], 1, 0);
  $pdf->Cell(3);
  // Affichage numéro fiche tiv
  $pdf->SetFont('Times', 'B', 14);
  $pdf->Cell(30,10,utf8_decode("Fiche TIV n° "), 0, 0);
  $pdf->SetFont('Times',  '', 12);
  $pdf->Cell(15,8,$result[0], 1, 1);
  foreach(array("exterieur", "interieur", "filetage", "robineterie") as $element)
    $pdf->addAspectInformation($result[0], $element);
  // Ligne de séparation
  $pdf->Cell(0,2,"", 'B', 1, 1);
  // Conclusion + signature
  $pdf->Ln(1);
  $pdf->Cell(50,8,"Conclusions : ", 0, 0);
}

$pdf->Output();

?>