<h2>Déclaration d'un nouveau matériel</h2>
<form name="ajout_form" id="ajout_form" action="ajout_element.php" method="POST">
<p>Type d'élément à déclarer :
<select id="element" name="element">
<option>bloc</option>
<option>stab</option>
<option>detendeur</option>
</select>
<input type="submit" name="submit" onclick='return(confirm("Procéder à la création ?"));' value="Procéder à la création du nouvel élément"></p>
</form>
<h2>Préparation d'un TIV</h2>
<p>A noter que la préparation d'un TIV consiste à pré-affecter les blocs aux différentes personnes qui feront plus tard le TIV.</p>
<script>
  $(function() { $( "#date_tiv" ).datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    appendText: '(yyyy-mm-dd)',
  });
  $( "#tiv" ).datepicker({ altFormat: 'yyyy-mm-dd' });
});
</script>
<form name="preparation_tiv" id="preparation_tiv" action="preparation_tiv.php" method="POST">
<p>Date de préparation du TIV :<input type="text" name="date_tiv" id="date_tiv" size="10" value="0000-00-00"/>
<input type="submit" name="submit" onclick='return(confirm("Lancer la procédure ?"));' value="Procéder à la pré-affectation"> </p>
</form>
