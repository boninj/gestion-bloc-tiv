<?php
# Si parametre _GET present, on est peut-être utilisé par ajout_element.php
if(array_key_exists("element", $_GET)) {
  $element = $_GET['element'];
  $id = $_GET['id'];
}
$title = "Edition d'un $element - club Aqua Sénart";
include_once('head.inc.php');
include_once('definition_element.inc.php');
include_once('connect_db.inc.php');

$edit_class = get_element_handler($element, $db_con);

if($extra_info = $edit_class->getExtraInformation($id)) {
  print "<h2>Informations supplémentaires</h2>\n";
  print $extra_info;
}
print "<h2>".$edit_class->getEditLabel()."</h2>
<script type='text/javascript'>
  var retour;
  $.validator.messages.required = 'Champ obligatoire';
  $(document).ready(function(){
    $(':submit').click(function () {
      if(this.name == 'delete') {
        retour = './#$element';
      } else {
        retour = 'edit.php?element=$element&id=$id';
      }
    });
    $('#edit_form').validate({
".$edit_class->getFormsRules().",
      submitHandler: function(form) {
        $.post('process_element.php', $('#edit_form').serialize(), function(data) {
          $('#results').html(data);
          setTimeout('window.location.href = retour', 1000);
        });
      }
    });
  });
</script>\n";
print "<fieldset><legend>".$edit_class->getLegend($id)."</legend>\n";
print "<p id=\"results\"></p>\n";
print $edit_class->constructEditForm($id, "edit_form");
print "</fieldset>\n";
print get_journal_entry($db_con, $id, $element);

if($extra_operation = $edit_class->getExtraOperation($id)) {
  print "<h2>Opérations supplémentaires</h2>\n";
  print $extra_operation;
}

print "<p><a href='".$edit_class->getBackUrl()."'>".$edit_class->getUrlTitle()."</a></p>\n";
include_once('foot.inc.php');
?>