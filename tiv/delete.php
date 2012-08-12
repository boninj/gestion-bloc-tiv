<?php
if(array_key_exists("element", $_GET)) {
  $element = $_GET['element'];
  $id = $_GET['id'];
} else if(array_key_exists("element", $_POST)) {
  $element = $_POST['element'];
  $id = $_POST['id'];
}

$title = "Suppression $element - club Aqua Sénart";
include_once('head.inc.php');
include_once("connect_db.inc.php");
if(!$db_con->query("DELETE FROM $element WHERE id = '$id'")) {
  print "Erreur de suppression de l'élément $element dans la base de données";
} else {
  print "Suppression réussi de l'élément $element";
}

?><p><a href='./#<?php print $element;?>'>Retour à la liste des <?php print $element;?>s</a></p>
<?php
include_once('foot.inc.php');
?>