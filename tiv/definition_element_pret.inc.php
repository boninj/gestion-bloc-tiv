<?php
class pretElement extends TIVElement {
  var $_blocs;
  var $_detendeurs;
  var $_stabs;
  function pretElement($db_con = false) {
    parent::__construct($db_con);
    $this->_parent_url       = "./#personne";
    $this->_parent_url_label = "Plongeurs/inspecteurs TIV";
    $this->_creation_label = "Création d'un nouveau prêt";
    $this->_update_label = "Mettre à jour le prêt";
    $this->_elements = array("id" => "Réf.", "id_personne" => "Nom de l'emprunteur", "debut_pret" => "Date de début du prêt",
                             "fin_prevu" => "Fin prévu du prêt", "fin_reel" => "Fin réel du prêt", "etat" => "Status du prêt");
    $etat_possible = array("", "Sortie", "Rentré");
    $this->_forms = array(
      "id_personne"  => array("required", "text", "Nom de l'emprunteur"),
      "debut_pret"   => array("required", "date", "Date de début du prêt"),
      "fin_prevu"    => array("required", "date", "Date de fin prévu du prêt"),
      "fin_reel"     => array("required", "date", "Date réelle de fin du prêt"),
      "etat"         => array("required", $etat_possible, "Status du prêt"),
    );
    $this->_forms_rules = '
  debug: false,
  rules: {
    id_personne: {
        required: true,
    },
    debut_pret: {
        date: true,
        required: true,
    },
    fin_prevu: {
        date: true,
        required: true,
    },
    etat: {
        required: true,
    },
  }';
  }
  function getDBCreateQuery($id) {
    return "INSERT INTO ".$this->_name."(id,debut_pret,fin_prevu) VALUES($id,SYSDATE(), DATE_ADD(SYSDATE(), INTERVAL 31 DAY))";
  }
  function getFormInput($label, $value) {
    if($label === "id_personne") {
      $db_query = "SELECT id,nom FROM personne";
      $db_result = $this->_db_con->query($db_query);
      $options = array("" => "");
      while($result = $db_result->fetch_array()) {
        $options[$result["id"]] = $result["nom"];
      }
      return self::constructSelectInputLabels($label, $options, $value);
    }
    return parent::getFormInput($label, $value);
  }
}
?>