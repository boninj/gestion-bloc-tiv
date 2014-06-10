<?php
if(array_key_exists("element", $_GET)) {
  $element = $_GET["element"];
}
$extract_type = "xlsx";
if(array_key_exists("extract_type", $_GET)) {
  $extract_type = $_GET["extract_type"];
}
require_once('definition_element.inc.php');
require_once('connect_db.inc.php');

if(!isset($real_element)) $real_element = $element;
$element_class = get_element_handler($real_element, $db_con);
unset($real_element);

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
        die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator($nom_club)
                             ->setLastModifiedBy($nom_club)
                             ->setTitle("Extract $element")
                             ->setSubject("Extract $element/$nom_club")
                             ->setDescription("Extract des éléments $element du $nom_club.")
                             ->setKeywords("$nom_club $element")
                             ->setCategory("Extract");

// Ajout entete
$objPHPExcel->setActiveSheetIndex(0);
$header = array($element_class->getHeaderElements());
$objPHPExcel->getActiveSheet()->fromArray($header, NULL, 'A1');

// Ajout lignes
$db_result =  $element_class->_db_con->query($element_class->getDBQuery());
$to_display = array();
while($line = $db_result->fetch_array()) {
  if(!$element_class->isDisplayed($line) && !$element_class->_force_display) continue;
  $record = array();
  foreach($element_class->getElements() as $elt) {
    $record []= $line[$elt];
  }
  $to_display []= $record;
}
$objPHPExcel->getActiveSheet()->fromArray($to_display, NULL, 'A2');

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle("$element");

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client's web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$element.'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>