<?php
class detendeurElement extends TIVElement {
  function detendeurElement($db_con = false) {
    parent::__construct($db_con);
    $this->_update_label = "Mettre à jour le détendeur";
    $this->_elements = array("id" => "Réf.", "modele" => "Modèle", "id_1ier_etage" => "n° 1ier étage",
                             "id_2e_etage" => "n° 2ieme étage", "id_octopus" => "n° octopus", "date" => "Date",);
    $this->_forms = array(
      "modele"         => array("required", "number", "Modèle de détendeur"),
      "id_1ier_etage"  => array("required", false,    "Référence constructeur du 1ier étage"),
      "id_2e_etage"    => array("required", false,    "Référence constructeur du 2ieme étage"),
      "id_octopus"     => array("required", false,    "Référence constructeur de l'octopus"),
      "date"           => array("required", "date",   "Date de construction du détendeur"),
    );
    $this->_forms_rules = '
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
  }
}
?>