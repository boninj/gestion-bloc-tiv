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
  print "<div class='error'>Erreur de suppression de l'élément $element dans la base de données.</div>";
} else {
  print "<div class='ok'>Suppression réussi de l'élément $element</div>
<script>
setTimeout('window.location.href = \"./#$element\"', 1000);
</script>
<p>Vous allez être redirigé automatiquement dans une seconde. Si ce n'est pas le cas, 
cliquer sur le lien suivant : <a href='./#$element'>Retour à la liste des ".$element."s</a></p>\n";
}

include_once('foot.inc.php');
?>