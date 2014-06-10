<?php
if(array_key_exists("element", $_GET)) {
  $elements = $_GET["element"];
  if($elements === "_ALL_") {
    $elements = "bloc,detendeur,inspecteur_tiv,inspection_tiv,palme,personne,pret,stab";
    $file_name = "Extraction-complete";
  } else {
    $file_name = str_replace(",", "-", $elements);
  }
} else {
  print "Rien a faire.";
  exit();
}

require_once('definition_element.inc.php');
require_once('connect_db.inc.php');

/** Include PHPExcel */
if(!file_exists(dirname(__FILE__) . '/PHPExcel.php')) {
  $title = "Erreur d'installation : Absence de l'application PHPExcel.";
  include_once('head.inc.php');
  print "<div>Pas de generateur de fichier Excel present. ".
        "Merci de le telecharger sur <a href='http://phpexcel.codeplex.com/'>http://phpexcel.codeplex.com/</a> et de rendre ".
        "le fichier PHPExcel.php ainsi que le répertoire PHPExcel ".
        "accessible dans le repertoire racine de l'application TIV.</div>";
  include_once("foot.inc.php");
  exit();
}
require_once dirname(__FILE__) . '/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator($nom_club)
                             ->setLastModifiedBy($nom_club)
                             ->setTitle("Extract $elements")
                             ->setSubject("Extract $elements/$nom_club")
                             ->setDescription("Extract des éléments $elements du $nom_club.")
                             ->setKeywords("$nom_club $elements")
                             ->setCategory("Extract");

$header_style_array = array(
  'borders' => array(
    'outline' => array(
       'style' => PHPExcel_Style_Border::BORDER_THIN,
    ),
  ),
  'font'  => array(
    'bold'  => true,
    'color' => array('rgb' => 'FFFFFF'),
  ),
  'fill'  => array(
    'type' => PHPExcel_Style_Fill::FILL_SOLID,
    'color' => array('rgb' => 'AAAAAA'),
  ),
);

// Lancement de l'extraction des elements
$current_sheet_index = 0;
foreach(explode(",", $elements) as $element) {
  // Recuperation classe type courant
  if(!isset($real_element)) $real_element = $element;
  $element_class = get_element_handler($real_element, $db_con);
  unset($real_element);

  // Creation de la feuille (si index > 0)
  if($current_sheet_index > 0) $objPHPExcel->createSheet();
  // On se positionne sur la derniere feuille.
  $objPHPExcel->setActiveSheetIndex($current_sheet_index++);
  $current_sheet = $objPHPExcel->getActiveSheet();
  // Ajout entete
  $header = $element_class->getHeaderElements();
  $current_sheet->fromArray($header, NULL, 'A1');
  $current_sheet->getStyle("1")->applyFromArray($header_style_array);
  $current_sheet->getStyle("1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  // Recuperation contenu des lignes
  $db_result =  $element_class->_db_con->query($element_class->getDBQuery());
  $to_display = array();
  // Filtrage et stockage de toutes les lignes du tableau
  while($line = $db_result->fetch_array()) {
    if(!$element_class->isDisplayed($line) && !$element_class->_force_display) continue;
    $record = array();
    foreach($element_class->getElements() as $elt) {
      $record []= $line[$elt];
    }
    $to_display []= $record;
  }
  // Ajout de la grille dans la feuille Excel
  $current_sheet->fromArray($to_display, NULL, 'A2');
  // Positionnement de l'autosize
  foreach(range('A', chr(ord('A') + count($header) - 1)) as $columnID) {
    $current_sheet->getColumnDimension($columnID)->setAutoSize(true);
    $current_sheet->getStyle($columnID)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  }

  // Renommage
  $current_sheet->setTitle($element);
}
// Activation de la premiere feuille
$objPHPExcel->setActiveSheetIndex(0);

// Preparation entete
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
header('Cache-Control: max-age=0');
// Saloperie pour faire fonctionner IE 9 (heurk !)
header('Cache-Control: max-age=1');

// Specificite a la con pour IE + SSL
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

// Renvoie du resultat
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>