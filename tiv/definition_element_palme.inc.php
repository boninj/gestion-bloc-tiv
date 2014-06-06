<?php
class palmeElement extends TIVElement {
  function palmeElement($db_con = false) {
    parent::__construct($db_con);
    $this->_parent_url       = "./#materiel";
    $this->_parent_url_label = "<img src='images/materiel.png' /> Matériel";
    $this->_creation_label = "Création d'une paire de palme";
    $this->_update_label = "Mettre à jour la paire de palme";
    $this->_elements = array("id" => "Réf.", "modele" => "Modèle", "taille" => "Taille",
                             "date_achat" => "Date d'achat", "observation" => "Observation/Remarques");
    $this->_forms = array(
      "modele"         => array("required", "number", "Modèle de palme"),
      "taille"         => array("required", false,    "Taille des palmes"),
      "date_achat"     => array("required", "date",   "Date d'achat des palmes"),
      "observation"    => array("required", "text",   "Observation/Remarques"),
    );
    $this->_forms_rules = '
  debug: true,
  rules: {
    modele: {
        required: true,
    },
    taille: {
        required: true,
    },
    date_achat: {
        required: true,
        date: true
    },
  }';
  }
}
?>