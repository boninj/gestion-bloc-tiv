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
  function getBackUrl() {
    $url_retour = "#".$this->_name;
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
      $form_input = "<select id=\"$label\" name=\"$label\">\n";
      foreach($forms_definition[$label][1] as $option) {
        $selected = ($option === $value ? " selected='selected'" : "");
        $form_input .= "<option$selected>$option</option>\n";
      }
      $form_input .= "</select>\n";
    } elseif($forms_definition[$label][1] === "date") {
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
      $form_input .= "<input type=\"text\" name=\"$label\" id=\"$label\" size=\"10\" value=\"$value\"/>\n";
    } else {
      $form_input = "<input type=\"text\" name=\"$label\" id=\"$label\" size=\"30\" value=\"$value\"/>";
    }
    return $form_input;
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
    print "<input type='submit' name='lancer' value='Mettre à jour le/la ".$this->_name."'>\n";
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