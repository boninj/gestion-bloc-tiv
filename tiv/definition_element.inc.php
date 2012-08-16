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
  function TIVElement($db_con = false) {
    // Init chaîne de texte
    $this->_name = str_replace("Element", "", get_class($this));
    $this->_update_label    = "Mettre à jour le/la ".$this->_name;
    $this->_url_title_label = "Retour à la liste des ".$this->_name."s";
    $this->_legend_label    = "Édition du ".$this->_name." __ID__";
    $this->_back_url        = "./#".$this->_name;
    $this->_record_count = 0;
    $this->_tr_class = array("odd", "even");
    $this->_db_con = $db_con;
    $this->_elements = array();
  }
  function setDBCon($db_con) {
    $this->_db_con = $db_con;
  }
  function getElements() { return $this->_elements; }
  static function getFormsRules() { }
  static function getForms() { }
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
  function getHTMLTable($id, $label, $db_query = false, $read_only = false) {
    $table = $this->getJSOptions($id, $label);
    $table .= "<table cellpadding='0' cellspacing='0' border='0' class='display' id='$id'>\n";
    $table .= "  <thead>".$this->getHTMLHeaderTable($read_only)."</thead>\n";
    $table .= "  <tbody>\n";
    if(!$db_query) $db_query = "SELECT ".join(",", $this->getElements())." FROM ".$this->_name;
    $db_result =  $this->_db_con->query($db_query);
    $this->_record_count = 0;
    while($line = $db_result->fetch_array()) {
      $current_class = $this->_tr_class[$this->_record_count++ % count($this->_tr_class)];
      // Met à jour l'état de la ligne courante afin de rajouter des informations
      // et renvoie une classe d'affichage css en cas de modification
      // pour mettre en avant un bloc ayant passé sa date de TIV par exemple.
      $table .= $this->getHTMLLineTable($line, $read_only, $current_class);
    }

    $table .= "  </tbody>\n";
    $table .= "  <tfoot>".$this->getHTMLHeaderTable($read_only)."</tfoot>\n";
    $table .= "</table>\n";
    return $table;
  }
  function getJSOptions($id, $label) {
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
      'iDisplayLength': 25,
      'sPaginationType': 'full_numbers',
      'bJQueryUI': true,
    } );
  } );
</script>\n";
  }
  function getHTMLHeaderTable($read_only = false) {
    $header = "    <tr>\n      <th>";
    $header .= join("</th><th>", $this->getElements());
    if(!$read_only) $header .= "</th><th>Opérations";
    $header .= "</th>\n    </tr>\n";
    return $header;
  }
  function getHTMLLineTable(&$record, $read_only, $default_class) {
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
    if(!$read_only) {
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
  function getEditUrl($id) {
    $element_to_manage = "id=$id&element=".$this->_name;
    $delete_confirmation = "return(confirm(\"Suppression élément ".$this->_name." (id = $id) ?\"));";
    return "<a href='edit.php?$element_to_manage'>Edit</a> / <a style='color: #F33;' onclick='$delete_confirmation' href='delete.php?$element_to_manage'>Suppr.</a>";
  }
  function getBackUrl() {
    return $this->_back_url;
  }
  function getUrlTitle() {
    return $this->_url_title_label;
  }
  function getLegend($id) {
    return str_replace("__ID__", $id, $this->_legend_label);
  }
  function getElementLabel($label) {
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
    $form  = "<form name='$form_name' id='$form_name' action='$action' method='POST'>\n";
    $form .= "<input type='hidden' name='id' value='$id' />\n";
    $form .= "<input type='hidden' name='element' value='".$this->_name."' />\n";
    $form .= "<table>\n";
    $form .= "  <tbody>\n";
    foreach(array_keys($this->getForms()) as $elt) {
      $value = $this->_values[$elt];
      $form .= "<tr><td>".$this->getElementLabel($elt)."</td><td>".$this->getFormInput($elt, $value)."</td></tr>\n";
    }
    $form .= "  </tbody>\n";
    $form .= "</table>\n";
    $form .= "<input type='submit' name='lancer' value='".$this->getUpdateLabel()."'>\n";
    $form .= "</form>\n";
    return $form;
  }
}

include_once("definition_element_bloc.inc.php");
include_once("definition_element_detendeur.inc.php");
include_once("definition_element_stab.inc.php");
include_once("definition_element_inspecteur_tiv.inc.php");
include_once("definition_element_inspection_tiv.inc.php");

?>