<?php
$title = "Consultation d'une séance de TIV";
include_once('head.inc.php');
include_once('connect_db.inc.php');
include_once('definition_element.inc.php');

if(array_key_exists("date_tiv", $_GET)) {
  $date_tiv = $_GET['date_tiv'];
} else {
  $date_tiv = $_POST['date_tiv'];
}

print "<h2>Informations relatives à l'inspection TIV du $date_tiv</h2>\n";

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

print "Il est prévu d'inspecter $total blocs au total dont $reepreuve réépreuve(s) et ".$count_tiv." inspections TIV.";

print "<h2>Liste des inspections prévues pour le $date_tiv</h2>\n";

$db_query = "SELECT inspection_tiv.id, bloc.id, bloc.constructeur, bloc.marque, bloc.capacite, inspecteur_tiv.nom, bloc.date_derniere_epreuve ".
            "FROM inspection_tiv, bloc, inspecteur_tiv ".
            "WHERE inspection_tiv.date = '$date_tiv' AND id_bloc = bloc.id AND id_inspecteur_tiv = inspecteur_tiv.id ".
            "ORDER BY inspecteur_tiv.nom";

$element = "inspection_tiv";
$columns = array("Référence TIV", "Numéro du bloc", "Constructeur bloc", "Marque bloc", "Capacité bloc", "Nom de l'inspecteur TIV", "Date dernière épreuve");
include('table_creator.inc.php');

print "<p><a href='index.php#admin'>Revenir au menu administration</a></p>\n";
include_once('foot.inc.php');
?>