<?php
class inspection_tivElement extends TIVElement {
  var $_date;
  var $_columns;
  function inspection_tivElement($db_con = false, $date = false) {
    parent::__construct($db_con);
    $this->_update_label = "Mettre à jour les informations sur l&#145;inspection TIV";
    $this->_elements = array(
      "id", "id_bloc", "id_inspecteur_tiv", "date", "etat_exterieur", "remarque_exterieur", "etat_interieur",
      "remarque_interieur", "etat_filetage", "remarque_filetage", "etat_robineterie", "remarque_robineterie",
      "decision",);
    $this->_columns = array("Référence TIV", "Numéro du bloc", "Constructeur bloc", "Marque bloc", "Capacité bloc",
                            "Nom de l'inspecteur TIV", "Date dernière épreuve", "Date dernier TIV", "Décision");
    if(!$date) {
      if(array_key_exists("date", $_GET)) {
        $date = $_GET['date'];
      } else if(array_key_exists("date", $_POST)) {
        $date = $_POST['date'];
      }
    }
    $this->_date = $date;
  }
  function getExtraInformation($id) {
    $db_result = $this->_db_con->query("SELECT id_bloc,id_inspecteur_tiv FROM inspection_tiv WHERE id = $id");
    $result = $db_result->fetch_array();
    $extra_info = "<p><a href='edit.php?id=".$result[0]."&element=bloc'>Afficher la fiche du bloc</a></p>\n".
                  "<p><a href='edit.php?id=".$result[1]."&element=inspecteur_tiv'>Afficher la fiche de l'inspecteur TIV</a></p>\n".
                  "<p><a href='impression_fiche_tiv.php?id_bloc=".$result[0]."&date=".$this->_date."'>Extraire la fiche PDF de l'inspection TIV</a></p>";
    return $extra_info;
  }
  function getExtraOperation($id) {
    $db_query = "SELECT date_dernier_tiv FROM bloc,inspection_tiv WHERE inspection_tiv.id = $id AND id_bloc = bloc.id AND date_dernier_tiv < '".$this->_date."'";
    $db_result = $this->_db_con->query($db_query);
    if(!$db_result->fetch_array()) {
      return "Pas d'opération supplémentaire possible. La date de cette fiche TIV est inférieur/égale à la dernière date TIV du bloc.";
    }
    $db_query = "SELECT decision FROM inspection_tiv WHERE id = $id AND decision = 'OK'";
    $db_result = $this->_db_con->query($db_query);
    if(!$db_result->fetch_array()) {
      return "Pas d'opération supplémentaire possible. Veuillez changer la décision afin de pouvoir mettre à jour le bloc.";
    }
    $form  = "<form name='update_bloc' id='update_bloc' action='update_bloc_tiv.php' method='POST'>\n";
    $form .= "<input type='hidden' name='date_tiv' value='".$this->_date."' />\n";
    $form .= "<input type='hidden' name='blocs_to_update[]' value='$id' />\n";
    $form .= "<input type='submit' name='lancer' value='Lancer la mise à jour du bloc avec le contenu de cette fiche TIV'>\n";
    $form .= "</form>\n";
    return $form;
  }
  function setDate($date) {
    $this->_date = $date;
  }
  function getHTMLHeaderTable($read_only = false) {
    $header = "    <tr>\n      <th>";
    $header .= join("</th><th>", $this->_columns);
    if(!$read_only) $header .= "</th><th>Opérations";
    $header .= "</th>\n    </tr>\n";
    return $header;
  }
  function getHTMLLineTable(&$record, $read_only, $default_class) {
    $current_class = $default_class;
    $line = "    <tr class=\"$current_class\">\n      <td>";
    $id = $record[0];
    $to_display = array();
    for($i = 0; $i < count($this->_columns); $i++) {
      $to_display []= $record[$i];
    }
    if(!$read_only) {
      $to_display [] = $this->getEditUrl($id);
    }
    $line .= implode("</td><td>", $to_display);
    $line .= "</td>\n    </tr>\n";
    return $line;
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
      $db_query = "SELECT id,id_club,constructeur,marque,capacite,numero FROM bloc";
      $db_result = $this->_db_con->query($db_query);
      $options = array("" => "");
      while($result = $db_result->fetch_array()) {
        $options[$result["id"]] = "n° ".$result["id_club"]. " (id=".$result["id"].") - ".
                                $result["constructeur"]." (".$result["marque"].") - capacité (litres) : ".$result["capacite"]." - n° série : ".$result["numero"];
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
  function getUrlTitle() {
    return "Retour à la liste des fiches d'inspections TIV du ".$this->_date;
  }
  function getBackUrl() {
    $url_retour = "consultation_tiv.php?date_tiv=".$this->_date;
    return $url_retour;
  }
}
?>