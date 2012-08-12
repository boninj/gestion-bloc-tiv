<?php
# Si parametre _GET present, on est peut-être utilisé par ajout_element.php
if(array_key_exists("element", $_GET)) {
  $element = $_GET['element'];
  $id = $_GET['id'];
}
$title = "Edition d'un $element - club Aqua Sénart";
include_once('head.inc.php');
include_once('config.inc.php');
require_once('connect_db.inc.php');

$to_retrieve = $element."_forms";
$forms_definition = $$to_retrieve;
$db_result =  $db_con->query("SELECT ".implode(",", array_keys($forms_definition))." FROM $element WHERE id = $id");
$values = $db_result->fetch_array();
?>
<script type="text/javascript">
  $.validator.messages.required = "Champ obligatoire";
  $(document).ready(function(){
    $("#edit_form").validate({
<?php echo $bloc_rules; ?>,
      messages: { required: 'toto' },
      submitHandler: function(form) {
        // do other stuff for a valid form
        $.post('process_element.php', $("#edit_form").serialize(), function(data) {
          $('#results').html(data);
        });
      }
    });
  });
</script>
<fieldset><legend>Édition du <?php print $element." $id";?></legend>
<div id='results'></div>
<form name="edit_form" id="edit_form" action="" method="POST">
<input type="hidden" name="id" value="<?php print $id; ?>" />
<input type="hidden" name="element" value="<?php print $element; ?>" />
<table>
<tbody>
<?php
foreach(array_keys($forms_definition) as $elt) {
  $value = $values[$elt];
  print "<tr><td>".$forms_definition[$elt][2]."</td><td>";
  if(is_array($forms_definition[$elt][1])) {
    print "<select id=\"$elt\" name=\"$elt\">\n";
    foreach($forms_definition[$elt][1] as $option) {
      $selected = ($option === $value ? " selected='selected'" : "");
      print "<option$selected>$option</option>\n";
    }
    print "</select>\n";
  } elseif($forms_definition[$elt][1] === "date") {
    print "<script>
  $(function() { $( \"#$elt\" ).datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    appendText: '(yyyy-mm-dd)',
  });
  $( \"#$elt\" ).datepicker({ altFormat: 'yyyy-mm-dd' });
});
</script>\n";
    print "<input type=\"text\" name=\"$elt\" id=\"$elt\" size=\"10\" value=\"$value\"/>\n";
  } else {
    print "<input type=\"text\" name=\"$elt\" id=\"$elt\" size=\"30\" value=\"$value\"/>";
  }
  print "</td></tr>\n";
}
?>
  </tbody>
  </table>
  <input type="submit" name="submit" value="Mettre à jour le/la <?php print $element;?>"> 
</form>
</fieldset>
<p><a href='./#<?php print $element;?>'>Retour à la liste des <?php print $element;?>s</a></p>
<?php
include_once('foot.inc.php');
?>