<?php
class personneElement extends TIVElement {
  function personneElement($db_con = false) {
    parent::__construct($db_con);
    $this->_parent_url       = "./#personne";
    $this->_parent_url_label = "<img src='images/personne.png' /> Plongeurs/inspecteurs TIV";
    $this->_creation_label = "Création d'une nouvelle personne";
    $this->_update_label = "Mettre à jour la personne";
    $this->_elements = array("id" => "Réf.", "nom" => "Prénom Nom", "adresse" => "Adresse de la personne",
                             "telephone" => "Téléphone de la personne",);
    $this->_forms = array(
      "nom"       => array("required", "text", "Nom/Prénom de la personne"),
      "adresse"   => array("required", "text", "Adresse de la personne"),
      "telephone" => array("required", "text", "Téléphone de la personne"),
    );
    $this->_forms_rules = '
  debug: false,
  rules: {
    nom: {
        required: true,
    },
    adresse: {
        required: true,
    },
    telephone: {
        required: true,
    },
  }';
  }
  function getQuickNavigationFormInput() {
    $input  = " > Navigation rapide<select name='id' onchange='this.form.submit()'>\n".
              "<option></option>\n";
    $db_result = $this->_db_con->query("SELECT id,nom FROM ".$this->_name);
    while($result = $db_result->fetch_array()) {
      $selected = ($result['id'] == $_GET['id'] ? " selected" : "");
      $input .= "<option value='".$result['id']."'$selected>".$result['nom']." (id n° ".$result['id'].")</option>\n";
    }
    $input .= "</select></p>".
              "</form>";
    return $input;
  }
}
?>