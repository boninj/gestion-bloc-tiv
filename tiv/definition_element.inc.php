<?php
include_once("configuration.inc.php");

function get_element_handler($element, &$db_con) {
  $class_element = $element."Element";
  $to_retrieve = "\$edit_class = new $class_element(\$db_con);";
  eval($to_retrieve);
  return $edit_class;
}

class TIVElement {
  var $_name;
  var $_values;
  var $_db_con;
  var $_update_label;
  var $_record_count;
  var $_tr_class;
  var $_elements;
  var $_forms;
  var $_forms_rules;
  var $_creation_label;
  var $_edit_label;
  var $_delete_label;
  var $_delete_message;
  var $_show_delete_form;
  var $_read_only;
  var $_show_create_form;
  var $_force_display;
  var $_parent_url;
  var $_parent_url_label;
  var $_url_title_label;
  var $_legend_label;
  function TIVElement($db_con = false) {
    // Init chaîne de texte
    $this->_name = str_replace("Element", "", get_class($this));
    $this->_update_label     = "Mettre à jour le/la ".$this->_name;
    $this->_url_title_label  = "liste des ".$this->_name."s";
    $this->_legend_label     = "Édition du ".$this->_name." __ID__";
    $this->_back_url         = "affichage_element.php?element=".$this->_name;
    $this->_parent_url       = "./";
    $this->_parent_url_label = "Accueil";
    $this->_record_count = 0;
    $this->_tr_class = array("odd", "even");
    $this->_db_con = $db_con;
    $this->_elements = array();
    $this->_forms = array();
    $this->_forms_rules = "";
    $this->_creation_label = "Création d'un(e) ".$this->_name;
    $this->_edit_label = "Édition d'un élément (".$this->_name.")";
    $this->_delete_label = "Supprimer cet élément";
    $this->_delete_message = "Lancer la suppression ?";
    $this->_show_delete_form = false;
    $this->_read_only = false;
    $this->_show_create_form = true;
    $this->_force_display = false;
  }
  function setDBCon($db_con) {
    $this->_db_con = $db_con;
  }
  function getElements() { return array_keys($this->_elements); }
  function getHeaderElements() { return array_values($this->_elements); }
  function getFormsRules() { return $this->_forms_rules; }
  function getForms() { return $this->_forms; }
  static function constructTextInput($label, $size, $value) {
    $form_input = "<input type=\"text\" name=\"$label\" id=\"$label\" size=\"$size\" value=\"$value\"/>\n";
    return $form_input;
  }
  static function constructSelectInputLabels($label, $labels, $value) {
    $form_input = "<select id=\"$label\" name=\"$label\">\n";
    foreach(array_keys($labels) as $option) {
      $selected = ($option == $value ? " selected='selected'" : "");
      $form_input .= "<option value='$option'$selected>".$labels[$option]."</option>\n";
    }
    $form_input .= "</select>\n";
    return $form_input;
  }
  static function constructSelectInput($label, $options, $value) {
    $labels = array();
    foreach($options as $opt) { $labels[$opt] = $opt; }
    return self::constructSelectInputLabels($label, $labels, $value);
  }
  static function constructDateInput($label, $value) {
    $form_input = "
    <script>
    $(function() {
      $( \"#$label\" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        appendText: '(yyyy-mm-dd)',
      });
      $( \"#$label\" ).datepicker({ altFormat: 'yyyy-mm-dd' });
    });
    </script>\n";
    $form_input .= self::constructTextInput($label, 10, $value);
    return $form_input;
  }
  function updateRecord(&$record) {
    return false;
  }
  function getDBQuery() {
    return "SELECT ".join(",", $this->getElements())." FROM ".$this->_name;
  }
  function getDBCreateQuery($id) {
    return "INSERT INTO ".$this->_name."(id) VALUES($id)";
  }
  function createDBRecord() {
    $db_result =  $this->_db_con->query("SELECT max(id) + 1 FROM ".$this->_name);
    $tmp = $db_result->fetch_array();
    $id = $tmp[0];
    if(!$id) $id = 1; // Si table vide assignation id à 1

    if($this->_db_con->query($this->getDBCreateQuery($id))) {
      add_journal_entry($this->_db_con, $id, $this->_name, "Création");
      return $id;
    }
    return false;
  }
  function deleteDBRecord($id) {
    $db_result =  $this->_db_con->query("DELETE FROM ".$this->_name." WHERE id = '$id'");

    if($db_result) {
      add_journal_entry($this->_db_con, $id, $this->_name, "Suppression");
      return $db_result;
    }
    return false;
  }
  function updateDBRecord($id, &$values) {
    $db_query = "SELECT ".implode(",", array_keys($this->_forms))." FROM ".$this->_name." WHERE id=$id";
    $db_result = $this->_db_con->query($db_query);
    if(!$result = $db_result->fetch_array()) {
      return false;
    }

    $to_set = array();
    foreach(array_keys($this->_forms) as $field) {
      if(strcmp($values[$field], $result[$field]) != 0) {
        $to_set[]= "$field = '".$this->_db_con->escape_string($values[$field])."'";
      }
    }
    if(count($to_set) > 0) {
      add_journal_entry($this->_db_con, $id, $this->_name, "Lancement d'une mise à jour (".implode(",", $to_set).")");
      $result = $this->_db_con->query("UPDATE ".$this->_name." SET ".implode(",", $to_set)." WHERE id = '$id'");
      return 1;
    }
    return 2;
  }
  function isDisplayed(&$record) {
    return true;
  }
  function getAdditionalControl() {
    if($this->_read_only || !$this->_show_create_form) return "";
    return '<form name="ajout_form" id="ajout_form" action="ajout_element.php" method="POST">
<input type="hidden" name="element" value="'.$this->_name.'" />
<input type="submit" name="submit" onclick=\'return(confirm("Procéder à la création ?"));\' value="'.$this->_creation_label.'"/>
</form>
';
  }
  function getHTMLTable($id, $label, $db_query = false, $show_additional_control = true) {
    $table = $this->getJSOptions($id, $label);
    if($show_additional_control)
      $table .= $this->getAdditionalControl($id);
    $table .= "<table cellpadding='0' cellspacing='0' border='0' class='display' id='$id'>\n";
    $table .= "  <thead>".$this->getHTMLHeaderTable()."</thead>\n";
    $table .= "  <tbody>\n";
    if(!$db_query) $db_query = $this->getDBQuery();
    $db_result =  $this->_db_con->query($db_query);
    $this->_record_count = 0;
    while($line = $db_result->fetch_array()) {
      if(!$this->isDisplayed($line) && !$this->_force_display) continue;
      $current_class = $this->_tr_class[$this->_record_count++ % count($this->_tr_class)];
      // Met à jour l'état de la ligne courante afin de rajouter des informations
      // et renvoie une classe d'affichage css en cas de modification
      // pour mettre en avant un bloc ayant passé sa date de TIV par exemple.
      $table .= $this->getHTMLLineTable($line, $current_class);
    }

    $table .= "  </tbody>\n";
    $table .= "  <tfoot>".$this->getHTMLHeaderTable()."</tfoot>\n";
    $table .= "</table>\n";
    return $table;
  }
  function getJSOptions($id, $label, $display = 25) {
    return "<script type='text/javascript' charset='utf-8'>
  $(document).ready(function() {
    $('#$id').dataTable( {
      'oLanguage': {
        'sZeroRecords': 'Pas de ".$label."s correspondants',
        'sInfo': 'Affichage des ".$label."s _START_ à _END_ sur _TOTAL_ ".$label."s',
        'sInfoEmpty': 'Aucun $label trouvé(e)',
        'sInfoFiltered': '(Suite à l&#145;application du filtre de recherche sur les _MAX_ ".$label."s)',
        'sSearch': 'Recherche d&#145;un $label :',
        'bLengthChange': true,
        'sLengthMenu': 'Afficher _MENU_ ".$label."s par page',
        'oPaginate': {
          'sFirst': 'Début',
          'sPrevious': 'Précédent',
          'sNext': 'Suivant',
          'sLast': 'Dernier',
        }
      },
      'iDisplayLength': $display,
      'sPaginationType': 'full_numbers',
      'bJQueryUI': true,
    } );
  } );
</script>\n";
  }
  function getHTMLHeaderTable() {
    $header = "    <tr>\n      <th>";
    $header .= join("</th><th>", $this->getHeaderElements());
    if(!$this->_read_only) $header .= "</th><th>Opérations";
    $header .= "</th>\n    </tr>\n";
    return $header;
  }
  function getHTMLLineTable(&$record, $default_class) {
    $current_class = $default_class;
    if($tmp = $this->updateRecord($record)) {
      $current_class = $tmp;
    }
    $line = "    <tr class=\"$current_class\">\n      <td>";
    $id = $record[0];
    $to_display = array();
    foreach($this->getElements() as $elt) {
      $to_display []= $record[$elt];
    }
    if(!$this->_read_only) {
      $to_display [] = $this->getEditUrl($id);
    }
    $line .= implode("</td><td>", $to_display);
    $line .= "</td>\n    </tr>\n";
    return $line;
  }
  function getExtraInformation($id) {
  }
  function getExtraOperation($id) {
  }
  function getURLReference($id) {
    return "id=$id&element=".$this->_name;
  }
  function getEditUrl($id) {
    $element_to_manage = $this->getURLReference($id);
    $delete_confirmation = "return(confirm(\"Suppression élément ".$this->_name." (id = $id) ?\"));";
    return "<a href='edit.php?$element_to_manage'>Edit</a> / <a style='color: #F33;' onclick='$delete_confirmation' href='delete.php?$element_to_manage'>Suppr.</a>";
  }
  function getParentUrl() {
    return "Navigation : <a href='./'>Accueil</a> > ".
           "<a href='".$this->_parent_url."'>".$this->_parent_url_label."</a>";
  }
  function getNavigationUrl() {
    return "<p>".$this->getParentUrl()." > <a href='".$this->getBackUrl()."'>".$this->getUrlTitle()."</a></p>\n";
  }
  function getBackUrl() {
    return $this->_back_url;
  }
  function getUrlTitle() {
    return $this->_url_title_label;
  }
  function getEditLabel() {
    return $this->_edit_label;
  }
  function getLegend($id) {
    return str_replace("__ID__", $id, $this->_legend_label);
  }
  function getElementLabel($label, $value) {
    $forms_definition = $this::getForms();
    return $forms_definition[$label][2];
  }
  function getFormInput($label, $value) {
    $forms_definition = $this::getForms();
    $form_input = "";
    if(is_array($forms_definition[$label][1])) {
      $form_input = $this->constructSelectInput($label, $forms_definition[$label][1], $value);
    } elseif($forms_definition[$label][1] === "date") {
      $form_input = $this->constructDateInput($label, $value);
    } else {
      $form_input = $this->constructTextInput($label, 30, $value);
    }
    return $form_input;
  }
  function getUpdateLabel() {
    return $this->_update_label;
  }
  function retrieveValues($id) {
    $db_query = "SELECT ".implode(",", array_keys($this::getForms()))." FROM ".$this->_name." WHERE id = $id";
    $db_result =  $this->_db_con->query($db_query);
    return $db_result->fetch_array();
  }
  function constructEditForm($id, $form_name, $action = "") {
    $this->_values = $this->retrieveValues($id);
    if(!$this->_values) return false;
    $form  = "<form name='$form_name' id='$form_name' action='$action' method='POST'>\n";
    $form .= "<input type='hidden' name='id' value='$id' />\n";
    $form .= "<input type='hidden' name='element' value='".$this->_name."' />\n";
    $form .= "<table>\n";
    $form .= "  <tbody>\n";
    foreach(array_keys($this->getForms()) as $elt) {
      $value = $this->_values[$elt];
      $form .= "<tr><td>".$this->getElementLabel($elt, $value)."</td><td>".$this->getFormInput($elt, $value)."</td></tr>\n";
    }
    $form .= "  </tbody>\n";
    $form .= "</table>\n";
    $form .= "<span style='height:0; width:0; overflow: hidden;'>\n";
    $form .= "  <button type='submit' value='default action'/>\n";
    $form .= "</span>\n";

    if($this->_show_delete_form) {
      $form .= "<input type='hidden' name='embedded' value='1' />\n"; // Utilisé pour détecter une suppression depuis le formulaire
      $form .= "<input type='submit' style='background: red;' name='delete' ".
               "value='".$this->_delete_label."' />\n";
    }
    $form .= "<input type='submit' name='lancer' value='".$this->getUpdateLabel()."' />\n";
    $form .= "</form>\n";
    return $form;
  }
}

include_once("definition_element_bloc.inc.php");
include_once("definition_element_detendeur.inc.php");
include_once("definition_element_stab.inc.php");
include_once("definition_element_inspecteur_tiv.inc.php");
include_once("definition_element_personne.inc.php");
include_once("definition_element_pret.inc.php");
include_once("definition_element_inspection_tiv.inc.php");
include_once("definition_element_journal_tiv.inc.php");

?>