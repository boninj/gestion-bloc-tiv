<?php
$title = "Liste des ".$element."s du club";
require_once('definition_element.inc.php');
require_once('connect_db.inc.php');

if(!isset($real_element)) $real_element = $element;

$class_element = $real_element."Element";
$to_retrieve = "\$element_class = new $class_element();";
unset($real_element);
eval($to_retrieve);

$element_class->setDBCon($db_con);

if($element === "inspection_tiv") {
  $element_class->setDate($date_tiv);
}

print $element_class->getHTMLTable("liste_$element", $element);
unset($db_query);
$i = $element_class->_record_count; // Hack baveux en attendant plus propre
?>