<?php
if(!isset($real_element)) $real_element = $element;

$class_element = $real_element."Element";
$to_retrieve = "\$element_class = new $class_element();";
unset($real_element);
eval($to_retrieve);
if(!@is_array($columns)) {
  $columns = $element_class::getElements();
}

if($element === "inspection_tiv") {
  $element_class->setDate($date_tiv);
}

?>
<script type="text/javascript" charset="utf-8">
  $(document).ready(function() {
    $('#liste_<?php print $element; ?>s').dataTable( {
      "oLanguage": {
        "sZeroRecords": "Pas de <?php print $element; ?>s correspondants",
        "sInfo": "Affichage des <?php print $element; ?>s _START_ à _END_ sur _TOTAL_ <?php print $element; ?>s",
        "sInfoEmpty": "Aucun <?php print $element; ?> trouvé",
        "sInfoFiltered": "(Suite à l'application du filtre de recherche sur les _MAX_ <?php print $element; ?>s)",
        "sSearch": "Recherche d'un <?php print $element; ?> :",
        "bLengthChange": true,
        "sLengthMenu": "Afficher _MENU_ <?php print $element; ?>s par page",
        "oPaginate": {
          "sFirst": "Début",
          "sPrevious": "Précédent",
          "sNext": "Suivant",
          "sLast": "Dernier",
        }
      },
      "iDisplayLength": 25,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
    } );
  } );
</script>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="liste_<?php print $element; ?>s">
  <thead>
    <tr>
      <th><?php
  if(!isset($read_only)) $read_only = false;
  print join("</th><th>", $columns);
  if(!$read_only) print "</th><th>Opérations";
?></th>
    </tr>
  </thead>
  <tbody>
<?php
if(!isset($db_query)) $db_query = "SELECT ".join(",", $columns)." FROM $element";
$db_result =  $db_con->query($db_query);
unset($db_query);
$tr_class = array("odd", "even");
$i = 0;
while($line = $db_result->fetch_array(MYSQLI_NUM)) {
  print "    <tr class=\"".$tr_class[$i++%2]."\">\n      <td>";
  $id = $line[0];
  if(!$read_only) {
    $line [] = $element_class->getEditUrl($id);
  }
  print join("</td><td>", $line);
  print "</td>\n    </tr>\n";
}

?>
  </tbody>
  <tfoot>
    <tr>
      <th><?php print join("</th><th>", $columns);?></th><th>Opérations</th>
    </tr>
  </tfoot>
</table>
<?php
unset($columns);
?>