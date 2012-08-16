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
print "<h2>Édition d'un l'élément</h2>\n";
?>
<script type="text/javascript">
  $.validator.messages.required = "Champ obligatoire";
  $(document).ready(function(){
    $("#edit_form").validate({
<?php print $edit_class->getFormsRules(); ?>,
      submitHandler: function(form) {
        $.post('process_element.php', $("#edit_form").serialize(), function(data) {
          $('#results').html(data);
          setTimeout("window.location.reload(true)", 1000);
        });
      }
    });
  });
</script>
<?php
print "<fieldset><legend>".$edit_class->getLegend($id)."</legend>\n";
print "<p id=\"results\"></p>\n";
print $edit_class->constructEditForm($id, "edit_form");
print "</fieldset>\n";

if($extra_operation = $edit_class->getExtraOperation($id)) {
  print "<h2>Opérations supplémentaires</h2>\n";
  print $extra_operation;
}

print "<p><a href='".$edit_class->getBackUrl()."'>".$edit_class->getUrlTitle()."</a></p>\n";
include_once('foot.inc.php');
?>