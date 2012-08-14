<?php
$date_tiv = $_POST["date_tiv"];
$title = "Mise à jour des blocs du club suite à l'inspection TIV du $date_tiv";
require_once('definition_element.inc.php');

include_once('head.inc.php');

print "<h2>Mise à jour des blocs suite à l'inspection TIV du $date_tiv</h2>\n";
require_once('connect_db.inc.php');
$db_query = "SELECT id_bloc FROM inspection_tiv,bloc ".
            "WHERE date = '$date_tiv' AND decision = 'OK' AND date_dernier_tiv < '$date_tiv' AND id_bloc = bloc.id";

$db_result = $db_con->query($db_query);
while($result = $db_result->fetch_array()) {
  $db_query = "UPDATE bloc SET date_dernier_tiv = '$date_tiv' WHERE id = '".$result[0]."'";
  if(!$db_con->query($db_query)) {
    print "<div class='error'>Erreur de mise à jour du bloc '".$result[0]."'</div>\n";
  } else {
    print "<div class='ok'>Mise à jour du bloc '".$result[0]."' OK</div>\n";
  }
}
print "<p><a href='consultation_tiv.php?date_tiv=$date_tiv'>Revenir dans la liste des inspections TIV du $date_tiv</a></p>\n";

include_once('foot.inc.php');
?>