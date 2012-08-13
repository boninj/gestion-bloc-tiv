<?php
$stab_elements = array(
  "id",
  "modele",
  "taille",
);

$stab_rules = '
  debug: true,
  rules: {
    modele: {
        required: true,
    },
    taille: {
        required: true,
    },
  }';

$stab_taille = array("junior", "XS", "S", "M", "M/L", "L", "XL", "XXL");
$stab_forms = array(
  "modele"       => array("required", "number",      "Modèle de stab"),
  "taille"       => array("required", $stab_taille , "Taille de la stab"),
);

class stabElement extends TIVElement {
  function stabElement() {
  }
  static function getElements() {
    global $stab_elements; return $stab_elements;
  }
  static function getFormsRules() {
    global $stab_rules; return $stab_rules;
  }
  static function getForms() {
    global $stab_forms; return $stab_forms;
  }
}
?>