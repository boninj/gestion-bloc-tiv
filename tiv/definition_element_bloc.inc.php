<?php
class blocElement extends TIVElement {
  var $_current_time;
  function blocElement($db_con = false) {
    parent::__construct($db_con);
    $this->_update_label = "Mettre à jour le bloc";
    $this->_current_time = time();
  }
  function constructResume($table_label, $time, $column, $div_label_to_update, $error_label, $error_class) {
    $db_query = "SELECT ".join(",", blocElement::getElements())." FROM bloc ".
                "WHERE $column < '".date("Y-M-D", $time)."'";
    $table_code = $this->getHTMLTable($table_label, $this->_name, $db_query);
    if($this->_record_count > 0) {
      $message_alerte = str_replace("__COUNT__", $this->_record_count, $error_label);
    }
    $html_code = "<p><div class='$error_class'>$message_alerte</div></p>\n";
    $html_code .= "<script>
$('#$div_label_to_update').html(\"$message_alerte\");
document.getElementById('$div_label_to_update').className='$error_class';
</script>\n";
    return $html_code.$table_code;
  }
  function updateRecord(&$record) {
    // Test présence champ nécessaire aux tests à réaliser
    foreach(array("date_dernier_tiv", "date_derniere_epreuve") as $elt) {
      if(!array_key_exists($elt, $record)) { return false; }
    }
    // Calcul sur le temps prochaine épreuve
    $date_derniere_epreuve = strtotime($record["date_derniere_epreuve"]);
    $date_prochaine_epreuve = strtotime("+5 years", $date_derniere_epreuve);
    if($date_prochaine_epreuve < $this->_current_time) {
      $record["date_derniere_epreuve"] = "<div class='error'>".$record["date_derniere_epreuve"]."</label>";
      return "critical-epreuve";
    }
    // Calcul sur le temps prochain TIV
    $date_dernier_tiv = strtotime($record["date_dernier_tiv"]);
    $date_prochain_tiv = strtotime("+1 years", $date_dernier_tiv);
    if($date_prochain_tiv < $this->_current_time) {
      $record["date_dernier_tiv"] = "<div class='error'>".$record["date_dernier_tiv"]."</label>";
      return "critical-tiv";
    }
    // Calcul sur le temps prochain TIV dans moins d'un mois
    $date_prochain_tiv_minus_one_month = strtotime("-1 month", $date_prochain_tiv);
    if($date_prochain_tiv_minus_one_month < $this->_current_time) {
      $record["date_dernier_tiv"] = "<div class='warning'>".$record["date_dernier_tiv"]."</label>";
      return "warning-tiv";
    }
  }
  function getTIVForm($id) {
    $form = "<script>
  $(function() {
    $.validator.messages.required = 'Champ obligatoire';
    $('#preparation_tiv').validate({
      debug: false,
      rules: {
        date_tiv: {
            required: true,
            date: true,
        },
      },
      submitHandler: function(form) {
        form.submit();
      }
    });
  });
</script>
<h3>Création d'une fiche TIV individuelle</h3>
<form name='preparation_tiv' id='preparation_tiv' action='preparation_tiv.php' method='POST'>
<input type='hidden' name='id_bloc' value='$id'/>
<script>
$(function() {
  $( '#admin-date-tiv-selector' ).datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    appendText: '(yyyy-mm-dd)',
  });
  $( '#admin-date-tiv-selector' ).datepicker({ altFormat: 'yyyy-mm-dd' });
});
</script>
<p>Date de l'inspection TIV :<input type='text' name='date_tiv' id='admin-date-tiv-selector' size='10' value=''/>
- Nom de l'inspecteur TIV : <select id='tivs' name='tivs[]'>
  <option></option>\n";
    $db_result = $this->_db_con->query("SELECT id,nom,actif FROM inspecteur_tiv WHERE actif = 'oui' ORDER BY nom");
    while($result = $db_result->fetch_array()) {
      $form .= "  <option value='".$result["id"]."'>".$result["nom"]."</option>\n";
    }
    $form .= "</select>
</div>
<input type='submit' name='lancer' value='Créer la fiche TIV' /></p>
</form>";
    return $form;
  }
  function getExtraInformation($id) {
    // Recherche d'info sur les dates d'epreuves et dernière inspection
    $db_result = $this->_db_con->query("SELECT date_derniere_epreuve,date_dernier_tiv FROM bloc WHERE id = $id");
    $result = $db_result->fetch_array();
    $derniere_epreuve = strtotime($result[0]);
    $next_epreuve = strtotime("+5 years", $derniere_epreuve);
    $next_epreuve_minus_one = strtotime("+4 years", $derniere_epreuve);
    $message_expiration = false;
    if($next_epreuve < time()) {
      $message_expiration = "<div class='error'>ATTENTION !!! CE BLOC A DÉPASSÉ SA DATE DE RÉÉPREUVE (".date("d/m/Y", $next_epreuve).") !!!</div>\n";
    } else if($next_epreuve_minus_one < time()) {
      $message_expiration = "<div class='warning'>Attention, ce bloc va dépassé sa date de réépreuve dans moins de 1 an (".date("d/m/Y", $next_epreuve).")</div>\n";
    }
    // Récupération d'information sur les fiches TIV du bloc
    $db_result = $this->_db_con->query("SELECT id,date FROM inspection_tiv WHERE id_bloc = $id ORDER BY date DESC");
    $extra_info = array();
    while($result = $db_result->fetch_array()) {
      $extra_info []= "<a href='edit.php?id=".$result[0]."&element=inspection_tiv&date=".$result[1]."'>Inspection TIV du ".$result[1]."</a> ".
                      "<a href='impression_fiche_tiv.php?id_bloc=$id&date=".$result[1]."'>(fiche PDF)</a>";
    }
    // Composition des messages
    if($message_expiration) $message = "<p>$message_expiration</p>\n";
    if(count($extra_info) > 0) {
      $message .= "<h3>Liste des fiches d'inspection TIV associées au bloc :</h3>\n<ul>\n<li>".implode("</li>\n<li>", $extra_info)."</li>\n</ul>\n";
    } else {
      $message .= "<p>Pas de fiche d'inspection TIV associée au bloc.</p>";
    }
    $message .= $this->getTIVForm($id);
    return $message;
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
      "pression_service",
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