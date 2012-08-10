<?php
$title = "Liste des ".$element."s du club";
require_once('config.inc.php');
require_once('connect_db.inc.php');

$columns = get_columns_from_element($element);

include('table_creator.inc.php');
?>