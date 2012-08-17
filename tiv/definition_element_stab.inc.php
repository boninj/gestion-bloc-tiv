<?php
class stabElement extends TIVElement {
  function stabElement($db_con = false) {
    parent::__construct($db_con);
    $this->_update_label = "Mettre à jour la stab";
    $this->_elements =  array("id" => "id", "modele" => "Modèle", "taille" => "Taille",);
  }
  static function getFormsRules() {
    return '
  debug: true,
  rules: {
    modele: {
        required: true,
    },
    taille: {
        required: true,
    },
  }';
  }
  static function getForms() {
    $stab_taille = array("junior", "XS", "S", "M", "M/L", "L", "XL", "XXL");
    return array(
      "modele"       => array("required", "number",      "Modèle de stab"),
      "taille"       => array("required", $stab_taille , "Taille de la stab"),
    );
  }
}
?>