<?php
$title = "Préparation d'une séance de TIV";
include_once('head.inc.php');
include_once('connect_db.inc.php');

$tivs = $_POST["tivs"];
$date_tiv = $_POST["date_tiv"];
$time_tiv = strtotime($date_tiv);
$max_date = strtotime("-55 months", $time_tiv);

reset($tivs);
$db_result = $db_con->query("SELECT id FROM bloc ORDER BY capacite,constructeur");
while($bloc = $db_result->fetch_array()) {
  if(!($tiv = current($tivs))) {
    reset($tivs); $tiv = current($tivs);
  } else {
    next($tivs);
  }
  $request = "INSERT INTO inspection_tiv (id, id_bloc, id_inspecteur_tiv, date) VALUES ".
             "(0, ".$bloc["id"].", $tiv, '$date_tiv')";
  if(!$db_con->query($request)) {
    print "Erreur lors de la preparation des TIVs.";
    print "<pre>$request</pre>";
  }
}

include_once('consultation_tiv.php');
include_once('foot.inc.php');
?>