<?php
class blocElement extends TIVElement {
  function blocElement() {
    parent::__construct();
  }
  function getExtraInformation($id) {
    $db_result = $this->_db_con->query("SELECT id,date FROM inspection_tiv WHERE id_bloc = $id ORDER BY date");
    $extra_info = array();
    while($result = $db_result->fetch_array()) {
      $extra_info []= "<a href='edit.php?id=".$result[0]."&element=inspection_tiv&date=".$result[1]."'>Inspection TIV du ".$result[1]."</a> ".
                      "<a href='impression_fiche_tiv.php?id_bloc=$id&date=".$result[1]."'>(fiche PDF)</a>";
    }
    return "<h3>Liste des fiches d'inspection TIV associées au bloc :</h3>\n<ul>\n<li>".implode("</li>\n<li>", $extra_info)."</li>\n</ul>\n";
  }
  function getUpdateLabel() {
    return "Mettre à jour le bloc";
  }
  static function getElements() {
    return array(
      "id",
      "id_club",
      "nom_proprietaire",
      "adresse",
      "constructeur",
      "marque",
      "numero",
      "capacite",
      "date_premiere_epreuve",
      "date_derniere_epreuve",
      "date_dernier_tiv",
      "pression_service"
    );
  }
  static function getFormsRules() {
    return '
  debug: true,
  rules: {
    club_id: {
        required: true,
    },
    nom_proprietaire: {
        required: true,
    },
    adresse: {
        required: true,
    },
    constructeur: {
        required: true,
    },
    marque: {
        required: true,
    },
    numero: {
        required: true,
    },
    capacite: {
        required: true,
    },
    date_premiere_epreuve: {
        required: true,
        date: true
    },
    date_derniere_epreuve: {
        required: true,
        date: true
    },
    date_dernier_tiv: {
        required: true,
        date: true
    },
    pression_service: {
        required: true,
        number: true
    },
  }';
  }
  static function getForms() {
    $bloc_capacite = array("6", "10", "12 long", "12 court", "15");
    $bloc_pression = array("150", "176", "200", "232", "300");
    $bloc_forms = array(
      "id_club"               => array("required", "number", "Référence du bloc au sein du club"),
      "nom_proprietaire"      => array("required", false,    "Nom du propriétaire du bloc"),
      "adresse"               => array("required", false,    "Adresse du propriétaire du bloc"),
      "constructeur"          => array("required", false,    "Constructeur du bloc (ex : ROTH)"),
      "marque"                => array("required", false,    "Marque du bloc (ex : Aqualung)"),
      "numero"                => array("required", false,    "Numéro de constructeur du bloc"),
      "capacite"              => array("required", $bloc_capacite,    "Capacité du bloc"),
      "date_premiere_epreuve" => array("required", "date",   "Date de la première épreuve du bloc"),
      "date_derniere_epreuve" => array("required", "date",   "Date de la dernière épreuve du bloc (tous les 5 ans)"),
      "date_dernier_tiv"      => array("required", "date",   "Date de la dernière inspection visuelle (tous les ans)"),
      "pression_service"      => array("required", $bloc_pression, "Pression de service du bloc (ex : 200 bars)"),
    );
    return $bloc_forms;
  }
}
?>