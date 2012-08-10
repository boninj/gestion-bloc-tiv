<?php
$title = "Application de gestion du matériel - club Aqua Sénart";
include_once('head.inc.php');
?>
<script>
  $(function() {
    $( "#MenuNavigation" ).tabs();
  });
</script>
<div id="MenuNavigation">
  <ul>
    <li><a href="#accueil"        title="Accueil gestion matériel"     >Accueil</a></li>
    <li><a href="#bloc"           title="Liste des blocs du club"      >Blocs</a></li>
    <li><a href="#detendeur"      title="Liste des détendeurs du club" >Détendeurs</a></li>
    <li><a href="#stab"           title="Liste des stabs du club"      >Stabs</a></li>
    <li><a href="#inspecteur_tiv" title="Liste des TIVs du club"       >Inspecteurs TIV</a></li>
    <li><a href="#bloc-rev"       title="Prochaine révisions des blocs">Status des blocs (TIV/ré-épreuve)</a></li>
  </ul>
  <div id="accueil">
    <?php include("accueil.php");?>
  </div>
  <div id="bloc">
    <?php $element = "bloc";           include("affichage_element.php");?>
  </div>
  <div id="detendeur">
    <?php $element = "detendeur";      include("affichage_element.php");?>
  </div>
  <div id="stab">
    <?php $element = "stab";           include("affichage_element.php");?>
  </div>
  <div id="inspecteur_tiv">
    <?php $element = "inspecteur_tiv"; include("affichage_element.php");?>
  </div>
  <div id="bloc-rev">
    <?php include("affichage_bloc_tiv.php"); ?>
  </div>
</div>
<?php
include_once('foot.inc.php');
?>