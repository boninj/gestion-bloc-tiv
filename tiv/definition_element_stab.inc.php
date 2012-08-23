<?php
class stabElement extends TIVElement {
  function stabElement($db_con = false) {
    parent::__construct($db_con);
    $this->_update_label = "Mettre à jour la stab";
    $this->_elements =  array("id" => "Réf.", "modele" => "Modèle", "taille" => "Taille",);
    $stab_taille = array("junior", "XS", "S", "M", "M/L", "L", "XL", "XXL");
    $this->_forms = array(
      "modele"       => array("required", "number",      "Modèle de stab"),
      "taille"       => array("required", $stab_taille , "Taille de la stab"),
    );
    $this->_forms_rules = '
  debug: false,
  rules: {
    modele: {
        required: true,
    },
    taille: {
        required: true,
    },
  }';
  }
}
?>