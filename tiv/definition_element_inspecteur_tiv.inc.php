<?php
$inspecteur_tiv_elements = array(
  "id",
  "nom",
  "numero_tiv",
  "adresse_tiv",
  "telephone_tiv",
  "actif",
);

$inspecteur_tiv_rules = '
  debug: true,
  rules: {
    nom: {
        required: true,
    },
    numero_tiv: {
        required: true,
    },
    adresse_tiv: {
        required: true,
    },
    telephone_tiv: {
        required: true,
    },
    actif: {
        required: true,
    },
  }';

$inspecteur_tiv_forms = array(
  "nom"           => array("required", "text",      "Nom de l'inspecteur TIV"),
  "numero_tiv"    => array("required", "text", "Numéro de TIV de l'inspecteur"),
  "adresse_tiv"   => array("required", "text", "Adresse du TIV"),
  "telephone_tiv" => array("required", "text", "Téléphone du TIV"),
  "actif"         => array("required", array("oui", "non"), "Le TIV est-il actif ?"),
);

class inspecteur_tivElement extends TIVElement {
  function inspecteur_tivElement() {
    parent::__construct();
  }
  function getUpdateLabel() {
    return "Mettre à jour l&#145;inspecteur TIV";
  }
  static function getElements() {
    global $inspecteur_tiv_elements; return $inspecteur_tiv_elements;
  }
  static function getFormsRules() {
    global $inspecteur_tiv_rules; return $inspecteur_tiv_rules;
  }
  static function getForms() {
    global $inspecteur_tiv_forms; return $inspecteur_tiv_forms;
  }
}
?>