<?php
# Si parametre _GET present, on est peut-être utilisé par ajout_element.php
if(array_key_exists("element", $_GET)) {
  $element = $_GET['element'];
  $id = $_GET['id'];
}
$title = "Edition d'un $element - club Aqua Sénart";
include_once('head.inc.php');
include_once('definition_element.inc.php');
require_once('connect_db.inc.php');

$class_element = $element."Element";
$to_retrieve = "\$edit_class = new $class_element();";
eval($to_retrieve);
$edit_class->setDBCon($db_con);
if($element === "inspection_tiv") {
  $edit_class->setDate($_GET["date"]);
}

?>
<script type="text/javascript">
  $.validator.messages.required = "Champ obligatoire";
  $(document).ready(function(){
    $("#edit_form").validate({
<?php echo $edit_class->getFormsRules(); ?>,
      submitHandler: function(form) {
        $.post('process_element.php', $("#edit_form").serialize(), function(data) {
          $('#results').html(data);
        });
      }
    });
  });
</script>
<?php
print $edit_class->constructEditForm($id, "edit_$element");
print "<p><a href='".$edit_class->getBackUrl()."'>".$edit_class->getUrlTitle()."</a></p>\n";
include_once('foot.inc.php');
?>