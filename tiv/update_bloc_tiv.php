<?php
$date_tiv = $_POST["date_tiv"];
$title = "Mise à jour des blocs du club suite à l'inspection TIV du $date_tiv";
require_once('definition_element.inc.php');

include_once('head.inc.php');

print "<h2>Mise à jour des blocs suite à l'inspection TIV du $date_tiv</h2>\n";
require_once('connect_db.inc.php');
$db_query = "SELECT id_bloc FROM inspection_tiv,bloc ".
            "WHERE date = '$date_tiv' AND decision = 'OK' AND date_dernier_tiv < '$date_tiv' AND id_bloc = bloc.id";

print "<a href='consultation_tiv.php?date_tiv=$date_tiv'>Revenir dans la liste des inspections TIV du $date_tiv</a>\n";

include_once('foot.inc.php');
?>