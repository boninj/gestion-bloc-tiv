<?php
$title = "Consultation d'une séance de TIV";
include_once('head.inc.php');
include_once('connect_db.inc.php');

if(array_key_exists("date_tiv", $_GET)) {
  $date_tiv = $_GET['date_tiv'];
} else {
  $date_tiv = $_POST['date_tiv'];
}

$db_query = "SELECT inspection_tiv.id, bloc.id, inspecteur_tiv.nom FROM inspection_tiv, bloc, inspecteur_tiv ".
            "WHERE inspection_tiv.date = '$date_tiv' AND id_bloc = bloc.id AND id_inspecteur_tiv = inspecteur_tiv.id ".
            "ORDER BY inspecteur_tiv.nom";

$element = "inspection_tiv";
$columns = array("Numéro du tiv", "Numéro club du bloc", "Nom de l'inspecteur TIV");
include('table_creator.inc.php');

include_once('foot.inc.php');
?>