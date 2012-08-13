<?php
include_once("definition_element.inc.php");
include_once("connect_db.inc.php");

$id = $_POST["id"];
$element = $_POST["element"];

$class_element = $element."Element";
$to_retrieve = "\$edit_class = new $class_element();";
eval($to_retrieve);

$forms_definition = $edit_class->getForms();

$to_set = array();
foreach(array_keys($forms_definition) as $field) {
  $to_set[]= "$field = '".$db_con->escape_string($_POST[$field])."'";
}

$result = $db_con->query("UPDATE $element SET ".implode(",", $to_set)." WHERE id = '$id'");
if($result) {
  print "<div class='ok'>Mise à jour OK</div>";
} else {
  print "<div class='error'>Problème lors de la mise à jour</div>";
}
?>