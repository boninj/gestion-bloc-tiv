<?php
$date_tiv = $_POST["date_tiv"];

$title = "Mise à jour des blocs du club suite à l'inspection TIV du $date_tiv";
require_once('definition_element.inc.php');

include_once('head.inc.php');

print "<h2>Mise à jour de(s) bloc(s) suite à l'inspection TIV du $date_tiv</h2>\n";
require_once('connect_db.inc.php');
if(array_key_exists("blocs_to_update", $_POST)) {
  $blocs_to_update = $_POST["blocs_to_update"];
  if(!is_array($blocs_to_update)) $blocs_to_update = array($blocs_to_update);
} else {
  $blocs_to_update = array();
  $db_query = "SELECT id_bloc FROM inspection_tiv,bloc ".
              "WHERE date = '$date_tiv' AND decision = 'OK' AND date_dernier_tiv < '$date_tiv' AND id_bloc = bloc.id";

  $db_result = $db_con->query($db_query);
  while($result = $db_result->fetch_array()) {
    $blocs_to_update []= $result[0];
  }
}

foreach($blocs_to_update as $bloc_id) {
  $db_query = "UPDATE bloc SET date_dernier_tiv = '$date_tiv' WHERE id = '$bloc_id'";
  if(!$db_con->query($db_query)) {
    print "<div class='error'>Erreur de mise à jour du bloc '$bloc_id'</div>\n";
  } else {
    print "<div class='ok'>Mise à jour du bloc '$bloc_id' OK</div>\n";
  }
}
print "<p><a href='consultation_tiv.php?date_tiv=$date_tiv'>Revenir dans la liste des inspections TIV du $date_tiv</a></p>\n";

include_once('foot.inc.php');
?>