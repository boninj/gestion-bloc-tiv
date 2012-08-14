<?php
class inspection_tivElement extends TIVElement {
  var $_date;
  function inspection_tivElement() {
    parent::__construct();
  }
  function getExtraInformation($id) {
    $db_result = $this->_db_con->query("SELECT id_bloc,id_inspecteur_tiv FROM inspection_tiv WHERE id = $id");
    $result = $db_result->fetch_array();
    $extra_info = "<p><a href='edit.php?id=".$result[0]."&element=bloc'>Afficher la fiche du bloc</a></p>\n".
                  "<p><a href='edit.php?id=".$result[1]."&element=inspecteur_tiv'>Afficher la fiche de l'inspecteur TIV</a></p>\n";
    return $extra_info;
  }
  function getUpdateLabel() {
    return "Mettre à jour les informations sur l&#145;inspection TIV";
  }
  function setDate($date) {
    $this->_date = $date;
  }
  function getFormInput($label, $value) {
    if($label === "id_inspecteur_tiv") {
      $db_query = "SELECT id,nom FROM inspecteur_tiv";
      $db_result = $this->_db_con->query($db_query);
      $options = array("" => "");
      while($result = $db_result->fetch_array()) {
        $options[$result["id"]] = $result["nom"];
      }
      return self::constructSelectInputLabels($label, $options, $value);
    } else if($label === "id_bloc") {
      $db_query = "SELECT id,id_club,constructeur,marque,capacite FROM bloc";
      $db_result = $this->_db_con->query($db_query);
      $options = array("" => "");
      while($result = $db_result->fetch_array()) {
        $options[$result["id"]] = "id = ".$result["id"]." (n° ".$result["id_club"].") - ".$result["constructeur"]." (".$result["marque"].") - ".$result["capacite"];
      }
      return self::constructSelectInputLabels($label, $options, $value);
    }
    return parent::getFormInput($label, $value);
  }
  function getEditUrl($id) {
    $element_to_manage = "id=$id&element=".$this->_name."&date=".$this->_date;
    $delete_confirmation = "return(confirm(\"Suppression élément ".$this->_name." (id = $id) ?\"));";
    return "<a href='edit.php?$element_to_manage'>Edit</a> / <a style='color: #F33;' onclick='$delete_confirmation' href='delete.php?$element_to_manage'>Suppr.</a>";
  }
  static function getElements() {
    return array(
      "id",
      "id_bloc",
      "id_inspecteur_tiv",
      "date",
      "etat_exterieur",
      "remarque_exterieur",
      "etat_interieur",
      "remarque_interieur",
      "etat_filetage",
      "remarque_filetage",
      "etat_robineterie",
      "remarque_robineterie",
      "decision",
    );
  }
  static function getFormsRules() {
    return '
  debug: true,
  rules: {
    id: {
        required: true,
    },
    id_bloc: {
        required: true,
    },
    id_inspecteur_tiv: {
        required: true,
    },
    date: {
        required: true,
        date: true,
    },
    etat_exterieur: {
        required: true,
    },
    etat_interieur: {
        required: true,
    },
    etat_filetage: {
        required: true,
    },
    etat_robineterie: {
        required: true,
    },
    decision: {
        required: true,
    },
  }';;
  }
  static function getPossibleStatus($grenaillage = false) {
    $etat_bloc = array("", "Bon", "A suivre", "Mauvais");
    if($grenaillage) $etat_bloc[] = "Grenaillage";
    return $etat_bloc;
  }
  static function getForms() {
    return array(
      "id_bloc"              => array("required", "text", "Numéro du bloc associé"),
      "id_inspecteur_tiv"    => array("required", "text", "Numéro de TIV de l'inspecteur"),
      "date"                 => array("required", "date", "Date de l'inspection TIV"),
      "etat_exterieur"       => array("required", self::getPossibleStatus(), "État externe du bloc"),
      "remarque_exterieur"   => array("required", false, "Remarque sur l'état externe du bloc"),
      "etat_interieur"       => array("required", self::getPossibleStatus(true), "État interne du bloc"),
      "remarque_interieur"   => array("required", false, "Remarque sur l'état interne du bloc"),
      "etat_filetage"        => array("required", self::getPossibleStatus(), "État du filetage du bloc"),
      "remarque_filetage"    => array("required", false, "Remarque sur le filetage du bloc"),
      "etat_robineterie"     => array("required", self::getPossibleStatus(), "État de la robineterie du bloc"),
      "remarque_robineterie" => array("required", false, "Remarque sur la robineterie du bloc"),
      "decision"             => array("required", array("OK", "Rebuté"), "Le bloc est-il accepté ?"),
    );
  }
  function getBackUrl() {
    $url_retour = "consultation_tiv.php?date_tiv=".$this->_date;
    return $url_retour;
  }
}
?>