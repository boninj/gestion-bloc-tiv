<?php
class masqueElement extends TIVElement {
  function masqueElement($db_con = false) {
    parent::__construct($db_con);
    $this->_parent_url       = "./#materiel";
    $this->_parent_url_label = "<img src='images/materiel.png' /> Matériel";
    $this->_creation_label = "Création d'un masque";
    $this->_update_label = "Mettre à jour un masque";
    $this->_elements = array("id" => "Réf.", "modele" => "Modèle", "taille" => "Taille",
                             "date_achat" => "Date d'achat", "observation" => "Observation/Remarques");
    $masque_tailles = array("", "XL", "L", "M", "S", "XS");
    $this->_forms = array(
      "modele"         => array("required", "number", "Modèle de masque"),
      "taille"         => array("required", $masque_tailles,    "Taille du masque"),
      "date_achat"     => array("required", "date",   "Date d'achat du masque"),
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