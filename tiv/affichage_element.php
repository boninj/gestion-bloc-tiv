<?php
$title = "Liste des ".$element."s du club";
require_once('definition_element.inc.php');
require_once('connect_db.inc.php');


$class_element = $element."Element";
$to_retrieve = "\$element_class = new $class_element();";
eval($to_retrieve);

$columns = $element_class::getElements();

include('table_creator.inc.php');
?>