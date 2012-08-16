<?php
require_once('definition_element.inc.php');
require_once('connect_db.inc.php');

$class_alerte_reepreuve = "ok";
$class_alerte_tiv       = "ok";

$message_alerte_reepreuve = "Rien à signaler concernant les réépreuves";
$message_alerte_tiv       = "Rien à signaler concernant les inspections TIV";

$max_repreuve_mois = "55";

$bloc_max_epreuve_age = strtotime("-$max_repreuve_mois month");
$db_query = "SELECT ".join(",", blocElement::getElements())." FROM bloc WHERE date_derniere_epreuve < '".date("Y-M-D", $bloc_max_epreuve_age)."'";

$element = "bloc_reepreuve";
$real_element = "bloc";
print "<h2>Blocs ayant une ré-épreuve de plus de $max_repreuve_mois mois</h2>\n";
include('table_creator.inc.php');
if($i > 0) {
  $message_alerte_reepreuve = "$i bloc(s) nécessite une ré-épreuve dans moins de 5 mois.";
  $class_alerte_reepreuve = "error";
}
print "<div class='$class_alerte_reepreuve'>$message_alerte_reepreuve</div>\n";
print "<script>
$('#message_important_reepreuve').html(\"$message_alerte_reepreuve\");
document.getElementById('message_important_reepreuve').className='$class_alerte_reepreuve';
</script>\n";

$bloc_max_tiv_age = strtotime("-11 month");
$db_query = "SELECT ".join(",", blocElement::getElements())." FROM bloc WHERE date_dernier_tiv < '".date("Y-M-D", $bloc_max_tiv_age)."'";

$element = "bloc_tiv";
$real_element = "bloc";
print "<h2>Blocs ayant besoin d'un TIV dans moins de 1 mois</h2>\n";
include('table_creator.inc.php');
if($i > 0) {
  $message_alerte_tiv = "$i bloc(s) nécessite une inspection TIV dans moins de 1 mois.";
  $class_alerte_tiv = "error";
}
print "<div class='$class_alerte_tiv'>$message_alerte_tiv</div>\n";

print "<script>
$('#message_important_tiv').html(\"$message_alerte_tiv\");
document.getElementById('message_important_tiv').className='$class_alerte_tiv';
</script>\n";

?>