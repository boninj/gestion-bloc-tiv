<?php
$element = $_POST["element"];
include_once("connect_db.inc.php");
$db_result =  $db_con->query("SELECT max(id) + 1 FROM $element;");
$tmp = $db_result->fetch_array(MYSQLI_NUM);
$id = $tmp[0];
if(!$id) { $id = 1; }

if(!$db_con->query("INSERT INTO $element(id) VALUES($id)")) {
  $title = "Erreur d'insertion d'un élément $element - club Aqua Sénart";
  include_once('head.inc.php');
  print "Erreur d'insertion du nouvel élément dans la base de données";
  include_once('foot.inc.php');
  exit(1);
}

include_once("edit.php");
?>