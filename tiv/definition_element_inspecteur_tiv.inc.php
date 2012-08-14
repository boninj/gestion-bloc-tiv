<?php
class inspecteur_tivElement extends TIVElement {
  function inspecteur_tivElement() {
    parent::__construct();
    $this->_update_label = "Mettre à jour l&#145;inspecteur TIV";
  }
  function getExtraInformation($id) {
    $db_query = "SELECT inspection_tiv.id,date,id_club ".
                "FROM inspection_tiv,bloc ".
                "WHERE id_inspecteur_tiv = $id AND id_bloc = bloc.id ORDER BY date, id_club";
    $db_result = $this->_db_con->query($db_query);
    $extra_info = array();
    while($result = $db_result->fetch_array()) {
      $extra_info []= "<a href='edit.php?id=".$result[0]."&element=inspection_tiv&date=".$result[1]."'>Inspection TIV du ".$result[1]." (bloc n° ".$result[2].")</a> ".
                      "<a href='impression_fiche_tiv.php?id_bloc=$id&date=".$result[1]."'>(fiche PDF)</a>";
    }
    return "<h3>Liste des fiches d'inspection TIV associées à l'inspecteur :</h3>\n<ul>\n<li>".implode("</li>\n<li>", $extra_info)."</li>\n</ul>\n";
  }
  static function getElements() {
    return array(
      "id",
      "nom",
      "numero_tiv",
      "adresse_tiv",
      "telephone_tiv",
      "actif",
    );
  }
  static function getFormsRules() {
    return '
  debug: true,
  rules: {
    nom: {
        required: true,
    },
    numero_tiv: {
        required: true,
    },
    adresse_tiv: {
        required: true,
    },
    telephone_tiv: {
        required: true,
    },
    actif: {
        required: true,
    },
  }';
  }
  static function getForms() {
    return array(
      "nom"           => array("required", "text",      "Nom de l'inspecteur TIV"),
      "numero_tiv"    => array("required", "text", "Numéro de TIV de l'inspecteur"),
      "adresse_tiv"   => array("required", "text", "Adresse du TIV"),
      "telephone_tiv" => array("required", "text", "Téléphone du TIV"),
      "actif"         => array("required", array("oui", "non"), "Le TIV est-il actif ?"),
    );
  }
}
?>