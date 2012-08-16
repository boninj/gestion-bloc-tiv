<?php
if(!isset($real_element)) $real_element = $element;
if(!isset($read_only)) $read_only = false;
if(!isset($db_query)) $db_query = false;

$class_element = $real_element."Element";
$to_retrieve = "\$element_class = new $class_element();";
unset($real_element);
eval($to_retrieve);

$element_class->setDBCon($db_con);

if($element === "inspection_tiv") {
  $element_class->setDate($date_tiv);
}

print $element_class->getHTMLTable($element, $read_only, $db_query);
unset($db_query);
$i = $element_class->_record_count; // Hack baveux en attendant plus propre
?>