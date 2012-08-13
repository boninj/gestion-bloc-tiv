<?php
$date_tiv = $_GET["date"];
require_once("gestion_impression.inc.php");
require_once("connect_db.inc.php");

$pdf = new PdfTIV($date_tiv, $db_con);

// Affiche un résumé du PDF
$pdf->addResume();
// Affiche les fiches des blocs à inspecter
$pdf->addBlocFile();

$pdf->Output();

?>