<?php
require_once('definition_element.inc.php');
require_once('connect_db.inc.php');

$message_alerte_reepreuve = "Rien à signaler concernant les réépreuves";
$message_alerte_tiv       = "Rien à signaler concernant les inspections TIV";

$bloc_element = new blocElement($db_con);

print $bloc_element->constructResume("blocs-reepreuve", strtotime("-55 month"), "date_derniere_epreuve", "message_important_reepreuve",
                                     "__COUNT__ bloc(s) nécessite une ré-épreuve dans moins de 5 mois.", 'error',
                                     "Rien à signaler concernant les réépreuves");

print $bloc_element->constructResume("blocs-tiv", strtotime("-11 month"), "date_dernier_tiv", "message_important_tiv",
                                     "__COUNT__ bloc(s) nécessite une inspection TIV dans moins de un mois.", 'warning',
                                     "Rien à signaler concernant les inspections TIV");

?>