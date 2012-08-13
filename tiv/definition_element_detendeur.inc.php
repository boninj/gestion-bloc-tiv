<?php
$detendeur_elements = array(
  "id",
  "modele",
  "id_1ier_etage",
  "id_2e_etage",
  "id_octopus",
  "date",
);
$detendeur_rules = '
  debug: true,
  rules: {
    modele: {
        required: true,
    },
    id_1ier_etage: {
        required: true,
    },
    id_2e_etage: {
        required: true,
    },
    id_octopus: {
        required: true,
    },
    date: {
        required: true,
        date: true
    },
  }';

$detendeur_forms = array(
  "modele"         => array("required", "number", "Modèle de détendeur"),
  "id_1ier_etage"  => array("required", false,    "Référence constructeur du 1ier étage"),
  "id_2e_etage"    => array("required", false,    "Référence constructeur du 2ieme étage"),
  "id_octopus"     => array("required", false,    "Référence constructeur de l'octopus"),
  "date"           => array("required", "date",   "Date de construction du détendeur"),
);

class detendeurElement extends TIVElement {
  function detendeurElement() {
    parent::__construct();
  }
  function getUpdateLabel() {
    return "Mettre à jour le détendeur";
  }
  static function getElements() {
    global $detendeur_elements; return $detendeur_elements;
  }
  static function getFormsRules() {
    global $detendeur_rules; return $detendeur_rules;
  }
  static function getForms() {
    global $detendeur_forms; return $detendeur_forms;
  }
}
?>