<?php
class TIVElement {
  var $_name;
  var $_values;
  var $_db_con;
  function TIVElement() {
    $this->_name = str_replace("Element", "", get_class($this));
  }
  function setDBCon($db_con) {
    $this->_db_con = $db_con;
  }
  static function getElements() { }
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
  function getEditUrl($id) {
    $element_to_manage = "id=$id&element=".$this->_name;
    $delete_confirmation = "return(confirm(\"Suppression élément ".$this->_name." (id = $id) ?\"));";
    return "<a href='edit.php?$element_to_manage'>Edit</a> / <a style='color: #F33;' onclick='$delete_confirmation' href='delete.php?$element_to_manage'>Suppr.</a>";
  }
  function getBackUrl() {
    $url_retour = "./#".$this->_name;
    return $url_retour;
  }
  function getUrlTitle() {
    return "Retour à la liste des ".$this->_name."s";
  }
  function getLegend($id) {
    return "Édition du ".$this->_name." $id";
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
    return "Mettre à jour le/la ".$this->_name;
  }
  function retrieveValues($id) {
    $db_query = "SELECT ".implode(",", array_keys($this::getForms()))." FROM ".$this->_name." WHERE id = $id";
    $db_result =  $this->_db_con->query($db_query);
    return $db_result->fetch_array();
  }
  function constructEditForm($id, $form_name, $action = "") {
    $this->_values = $this->retrieveValues($id);
    print "<fieldset><legend>".$this->getLegend($id)."</legend>\n";
    print "<div id='edit_".$this->_name."'></div>\n";
    print "<form name='$form_name' id='$form_name' action='$action' method='POST'>\n";
    print "<input type='hidden' name='id' value='$id' />\n";
    print "<input type='hidden' name='element' value='".$this->_name."' />\n";
    print "<table>\n";
    print "  <tbody>\n";
    foreach(array_keys($this->getForms()) as $elt) {
      $value = $this->_values[$elt];
      print "<tr><td>".$this->getElementLabel($elt)."</td><td>".$this->getFormInput($elt, $value)."</td></tr>\n";
    }
    print "  </tbody>\n";
    print "</table>\n";
    print "<input type='submit' name='lancer' value='".$this->getUpdateLabel()."'>\n";
    print "</form>\n";
    print "</fieldset>\n";
  }
}

include_once("definition_element_bloc.inc.php");
include_once("definition_element_detendeur.inc.php");
include_once("definition_element_stab.inc.php");
include_once("definition_element_inspecteur_tiv.inc.php");
include_once("definition_element_inspection_tiv.inc.php");

?>