<?php
include_once("definition_element.inc.php");
include_once("connect_db.inc.php");

$id = $_POST["id"];
$element = $_POST["element"];

$edit_class = get_element_handler($element, $db_con);

if($edit_class->updateDBRecord($id, $_POST)) {
  print "<div class='ok'>Mise à jour OK</div>";
} else {
  print "<div class='error'>Problème lors de la mise à jour</div>";
}
?>