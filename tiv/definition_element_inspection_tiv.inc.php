<?php
$inspection_tiv_elements = array(
  "id",
  "id_bloc",
  "id_inspecteur_tiv",
  "date",
  "etat_exterieur",
  "remarque_exterieur",
  "etat_interieur",
  "remarque_interieur",
  "etat_filetage",
  "remarque_filetage",
  "etat_robineterie",
  "remarque_robineterie",
  "decision",
);

$inspection_tiv_rules = '
  debug: true,
  rules: {
    id: {
        required: true,
    },
    id_bloc: {
        required: true,
    },
    id_inspecteur_tiv: {
        required: true,
    },
    date: {
        required: true,
        date: true,
    },
    etat_exterieur: {
        required: true,
    },
    etat_interieur: {
        required: true,
    },
    etat_filetage: {
        required: true,
    },
    etat_robineterie: {
        required: true,
    },
    decision: {
        required: true,
    },
  }';

$etat_bloc             = array("", "Bon", "A suivre", "Mauvais");
$etat_bloc_grenaillage = array("", "Bon", "A suivre", "Mauvais", "Grenaillage");
$inspection_tiv_forms  = array(
  "id_bloc"              => array("required", "text", "Numéro du bloc associé"),
  "id_inspecteur_tiv"    => array("required", "text", "Numéro de TIV de l'inspecteur"),
  "date"                 => array("required", "date", "Date de l'inspection TIV"),
  "etat_exterieur"       => array("required", $etat_bloc, "État externe du bloc"),
  "remarque_exterieur"   => array("required", false, "Remarque sur l'état externe du bloc"),
  "etat_interieur"       => array("required", $etat_bloc_grenaillage, "État interne du bloc"),
  "remarque_interieur"   => array("required", false, "Remarque sur l'état interne du bloc"),
  "etat_filetage"        => array("required", $etat_bloc, "État du filetage du bloc"),
  "remarque_filetage"    => array("required", false, "Remarque sur le filetage du bloc"),
  "etat_robineterie"     => array("required", $etat_bloc, "État du filetage du bloc"),
  "remarque_robineterie" => array("required", false, "Remarque sur le filetage du bloc"),
  "decision"             => array("required", array("OK", "Rebuté"), "Le bloc est-il accepté ?"),
);

class inspection_tivElement extends TIVElement {
  var $_date;
  function inspection_tivElement() {
    parent::__construct();
  }
  function setDate($date) {
    $this->_date = $date;
  }
  function getEditUrl($id) {
    $element_to_manage = "id=$id&element=".$this->_name."&date=".$this->_date;
    $delete_confirmation = "return(confirm(\"Suppression élément ".$this->_name." (id = $id) ?\"));";
    return "<a href='edit.php?$element_to_manage'>Edit</a> / <a style='color: #F33;' onclick='$delete_confirmation' href='delete.php?$element_to_manage'>Suppr.</a>";
  }
  static function getElements() {
    global $inspection_tiv_elements; return $inspection_tiv_elements;
  }
  static function getFormsRules() {
    global $inspection_tiv_rules; return $inspection_tiv_rules;
  }
  static function getForms() {
    global $inspection_tiv_forms; return $inspection_tiv_forms;
  }
  function getBackUrl() {
    $url_retour = "consultation_tiv.php?date_tiv=".$this->_date;
    return $url_retour;
  }
}
?>