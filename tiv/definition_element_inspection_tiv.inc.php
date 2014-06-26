<?php
class inspection_tivElement extends TIVElement {
  var $_date;
  var $_columns;
  function inspection_tivElement($db_con = false, $date = false) {
    parent::__construct($db_con);
    $this->_show_create_form = false;
    $this->_parent_url       = "./#admin";
    $this->_parent_url_label = "<img src='images/admin.png' /> Administration";
    $this->_update_label = "Mettre à jour les informations sur l&#145;inspection TIV";
    // Structure pour l'affichage des inspections dans l'interface Web
    $this->_elements = array(
      "id"                 => "Réf.",
      "info_bloc"          => "Info bloc",
      "constructeur"       => "Constructeur bloc",
      "marque"             => "Marque bloc",
      "capacite"           => "Capacité bloc",
      "nom"                => "Nom de l'inspecteur TIV",
      "date_derniere_epreuve" => "Date dernière épreuve",
      "date_dernier_tiv"   => "Date dernier TIV",
      "decision"           => "Décision",
      "remarque"           => "Remarque",
      "etat_exterieur"     => "Extérieur",
      "etat_interieur"     => "Intérieur",
      "etat_filetage"      => "Filetage",
      "etat_robineterie"   => "Robinet",
    );
    // Information pour le formulaire de modification
    $this->_forms = array(
      "id_bloc"              => array("required", "text", "Numéro du bloc associé"),
      "id_inspecteur_tiv"    => array("required", "text", "Numéro de TIV de l'inspecteur"),
      "date"                 => array("required", "date", "Date de l'inspection TIV"),
      "etat_exterieur"       => array("required", $this->getPossibleStatus(), "État externe du bloc"),
      "remarque_exterieur"   => array("required", false, "Remarque sur l'état externe du bloc"),
      "etat_interieur"       => array("required", $this->getPossibleStatus(true), "État interne du bloc"),
      "remarque_interieur"   => array("required", false, "Remarque sur l'état interne du bloc"),
      "etat_filetage"        => array("required", $this->getPossibleStatus(), "État du filetage du bloc"),
      "remarque_filetage"    => array("required", false, "Remarque sur le filetage du bloc"),
      "etat_robineterie"     => array("required", $this->getPossibleStatus(), "État de la robineterie du bloc"),
      "remarque_robineterie" => array("required", false, "Remarque sur la robineterie du bloc"),
      "decision"             => array("required", array("", "OK", "Rebuté"), "Le bloc est-il accepté ?"),
      "remarque"             => array("required", "text", "Commentaire sur l'inspection."),
    );
    $this->_forms_rules = '
  debug: false,
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
  }';
    if(!$date) {
      if(array_key_exists("date", $_GET)) {
        $date = $_GET['date'];
      } else if(array_key_exists("date", $_POST)) {
        $date = $_POST['date'];
      }
    }
    $this->_date = $date;
    $this->_url_title_label = "<img src='images/liste.png'/> Retour à la liste des fiches d'inspections TIV du ".$this->_date;
    $this->_back_url        = "consultation_tiv.php?date_tiv=".$this->_date;
  }
  function getURLReference($id) {
    return parent::getURLReference($id)."&date=".$this->_date;
  }
  function getExtraInformation($id) {
    $db_result = $this->_db_con->query("SELECT id_bloc,id_inspecteur_tiv FROM inspection_tiv WHERE id = $id");
    $result = $db_result->fetch_array();
    $extra_info = "<p>Navigation rapide : <a href='edit.php?id=".$result[0]."&element=bloc'>".
                  "<img src='images/edit.png' style='vertical-align:middle;' /> fiche du bloc</a> - ".
                  "<a href='edit.php?id=".$result[1]."&element=inspecteur_tiv'>".
                  "<img src='images/personne.png' style='vertical-align:middle;' /> fiche de l'inspecteur TIV</a> - ".
                  "<a href='impression_fiche_tiv.php?id_bloc=".$result[0]."&date=".$this->_date."'>".
                  "<img src='images/pdf.png' style='vertical-align:middle;' /> fiche au format PDF</a>";
    $extra_info .= "<script>
function tiv_tout_a_ok() {
  $('#etat_exterieur').val('Bon');
  $('#etat_interieur').val('Bon');
  $('#etat_filetage').val('Bon');
  $('#etat_robineterie').val('Bon');
  $('#decision').val('OK');
}
$('#switch_all_ok').live(\"click\", function() {
  tiv_tout_a_ok();
  return false;
});
</script>
";
    return $extra_info;
  }
  function getQuickNavigationFormInput() {
    $input  = " > Navigation rapide<select name='id' onchange='this.form.submit()'>\n".
              "<option></option>\n";
    $db_result = $this->_db_con->query("SELECT id FROM ".$this->getTableName()." WHERE date = '".$this->_date."' ORDER BY id");
    while($result = $db_result->fetch_array()) {
      $selected = ($result['id'] == $_GET['id'] ? " selected" : "");
      $input .= "<option value='".$result['id']."'$selected>".$this->_name." ".$result['id']."</option>\n";
    }
    $input .= "</select></p>";
    return $input;
  }
  function getNavigationUrl() {
    $input_form = $this->getQuickNavigationFormInput();
    return "<form action='edit.php' method='GET' style='display: inline!important;'>\n".
           "<input type='hidden' name='element' value='".$this->_name."' />\n".
           "<input type='hidden' name='date' value='".$this->_date."' />\n".
           "<p>".$this->getParentUrl()." > \n<a href='".$this->getBackUrl()."'>".$this->getUrlTitle()."</a>\n$input_form</p>\n</form>\n";
  }
  function getExtraOperation($id) {
    $db_query = "SELECT date_dernier_tiv ".
                "FROM bloc,inspection_tiv WHERE inspection_tiv.id = $id AND id_bloc = bloc.id ".
                "AND decision IN ('OK', 'Rebuté') ".
                "AND (date_dernier_tiv < inspection_tiv.date OR decision != bloc.etat)";
    $db_result = $this->_db_con->query($db_query);
    if(!$db_result->fetch_array()) {
      return "<div class='ok'>Pas d'opération possible. La date de cette fiche TIV est inférieur/égale à la dernière date TIV du bloc.</div>";
    }
    $db_query = "SELECT id_bloc,decision,date FROM inspection_tiv WHERE id = $id";
    $db_result = $this->_db_con->query($db_query);
    $result = $db_result->fetch_array();
    if(!$result) {
      return "<div class='warning'>Pas d'opération supplémentaire possible. Veuillez changer la décision à 'OK' afin de pouvoir mettre à jour le bloc.</div>";
    }
    $id_bloc = $result[0];
    $date_tiv = $result[2];
    $form  = "<form name='update_bloc' id='update_bloc' action='update_bloc_tiv.php' method='POST'>\n";
    $form .= "<input type='hidden' name='date_tiv' value='$date_tiv' />\n";
    $form .= "<input type='hidden' name='blocs_to_update[]' value='$id_bloc' />\n";
    $form .= "<input type='submit' name='lancer' value='Lancer la mise à jour du bloc avec le contenu de cette fiche TIV'>\n";
    $form .= "</form>\n";
    return $form;
  }
  function setDate($date) {
    $this->_date = $date;
  }
  function getDBQuery() {
    return "
SELECT
  inspection_tiv.id, CONCAT('Réf :', bloc.id, ' / n° club : ', bloc.id_club) as info_bloc,
  bloc.constructeur, bloc.marque, bloc.capacite, inspecteur_tiv.nom,
  bloc.date_derniere_epreuve, bloc.date_dernier_tiv,decision,remarque,
  etat_exterieur, etat_interieur, etat_filetage, etat_robineterie
FROM
  inspection_tiv
INNER JOIN bloc           ON inspection_tiv.id_bloc = bloc.id
LEFT  JOIN inspecteur_tiv ON inspection_tiv.id_inspecteur_tiv = inspecteur_tiv.id
WHERE
  inspection_tiv.date = '".$this->_date."'
ORDER BY
  inspecteur_tiv.nom";
  }
  function getFormInput($label, $value) {
    if($label === "id_inspecteur_tiv") {
      $db_query = "SELECT id,nom FROM inspecteur_tiv ORDER BY nom";
      $db_result = $this->_db_con->query($db_query);
      $options = array("" => "");
      while($result = $db_result->fetch_array()) {
        $options[$result["id"]] = $result["nom"];
      }
      return $this->constructSelectInputLabels($label, $options, $value);
    } else if($label === "id_bloc") {
      $db_query = "SELECT id,id_club,constructeur,marque,capacite,numero FROM bloc ORDER BY id_club";
      $db_result = $this->_db_con->query($db_query);
      $options = array("" => "");
      while($result = $db_result->fetch_array()) {
        $options[$result["id"]] = "n° ".$result["id_club"]. " (id=".$result["id"].") - ".
                                $result["constructeur"]." (".$result["marque"].") - capacité (litres) : ".$result["capacite"]." - n° série : ".$result["numero"];
      }
      return $this->constructSelectInputLabels($label, $options, $value);
    } else if(preg_match("/^etat_/", $label) || $label === "decision") {
      return parent::getFormInput($label, $value)."<a id='switch_all_ok'>Passer tous les champs à OK</a>";
    }
    return parent::getFormInput($label, $value);
  }
  function getPossibleStatus($grenaillage = false) {
    $etat_bloc = array("", "Bon", "A suivre", "Mauvais");
    if($grenaillage) $etat_bloc[] = "Grenaillage";
    return $etat_bloc;
  }
}
?>