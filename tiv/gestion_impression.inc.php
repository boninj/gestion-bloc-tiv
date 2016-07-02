<?php
require_once("definition_element.inc.php");
require_once("connect_db.inc.php");
require_once('fpdf17/fpdf.php');
require_once('fpdi/fpdi.php');

class PdfTIV extends FPDI {
  var $_date;
  var $_db_con;
  var $_tiv_template;
  var $_bloc_definition;
  var $_bloc_info_to_retrieve;
  var $_bloc_header_width;
  function PdfTIV($date, $db_con) {
    // Import variable globale
    global $nom_club;
    global $adresse_club;
    global $numero_club;
    global $GET;
    // Init classe
    $this->_debug = 0;
    $this->_date = $date;
    $this->_db_con = $db_con;
    // Information template fiche TIV
    $this->_tiv_template = array();
    $this->_tiv_template["file"] = "template-pdf/entete-inspection-TIV-idf.pdf";
    $this->_tiv_template["pied"] = "template-pdf/pied-page-TIV-idf.pdf";
    $this->_tiv_template["first_page_tiv_count"]   =  6;
    $this->_tiv_template["always_show_tiv_info"]   = false;
    if(array_key_exists("always_show_tiv_info", $_GET)) $this->_tiv_template["always_show_tiv_info"] = true;
    $this->_tiv_template["max_tiv_count_per_page"] = 13;
    $this->_tiv_template["interligne"]             =  7;
    $this->_tiv_template["interligne_bloc"]        =  10;
    $this->_tiv_template["start_info_bloc"]        = array(5, 100.1);
    // Informations globales sur la seance TIV
    $this->_tiv_template["champ"]["nom_club"]        = array(187, 36.5, 98, $nom_club);
    $this->_tiv_template["champ"]["numero_club"]     = array(187, 43.5, 98, $numero_club);
    $this->_tiv_template["champ"]["adresse_club"]    = array(187, 50.5, 98, $adresse_club);
    $this->_tiv_template["champ"]["date"]            = array(90,  43.5, 42, $date);
    // Information relative à l'inspecteur TIV
    $this->_tiv_template["result"]["numero_tiv"]     = array(100, 36.5, 32); # x, y, largeur
    $this->_tiv_template["result"]["nom"]            = array(60,  50.5, 72);
    $this->_tiv_template["result"]["adresse_tiv"]    = array(60,  57.5, 72);
    $this->_tiv_template["result"]["telephone_tiv"]  = array(60,  71.5, 72);
    // Requetes specifiques
    $bloc_count_query = "SELECT COUNT(inspection_tiv.id_bloc) FROM inspection_tiv WHERE id_inspecteur_tiv = ID_INSPECTEUR AND decision = 'OK' AND date = '$date'";
    $this->_tiv_template["query"]["bloc_count"]      = array(202, 79, 15, $bloc_count_query);
    // Information a afficher dans le tableau recapitulatif
    $this->_bloc_definition = array(
      # Entete => taille, champ_base
      "Fabricant"             => array(24, "constructeur"),
      "Marque"                => array(32, "marque"),
      "Numéro série Identification" => array(24, "numero"),
      "Date de 1ière requalification"  => array(24, "date_premiere_epreuve"),
      "Date dernière requalification"  => array(24, "date_derniere_epreuve"),
      "Date visite précédente"         => array(22, "date_dernier_tiv"),
      "Critères notables lors de la visite" => array(
        64, array(
          "Extérieur" => array(16, "etat_exterieur"),
          "Intérieur" => array(16, "etat_interieur"),
          "Filetage"  => array(16, "etat_filetage"),
          "Robinet"   => array(16, "etat_robineterie"),
        ),
      ),
      "Décision du TIV"       => array(24, "decision"),
      "Commentaires"          => array(42, "remarque")
    );
    parent::__construct();
    self::AliasNbPages();
  }
  function ClubHeader() {
    global $logo_club;
    global $nom_club;
    $this->Image($logo_club, 10, 6, 10);
    $this->SetFont('Arial','B',10);
    $this->Cell(10);
    $this->Cell(0, 8, utf8_decode('Fiche TIV du '.$this->_date." - club $nom_club"), 'B', 0, 'C');
    $this->Ln(11);
  }
  function addInspecteurResume() {
    $this->AddPage();
    $this->ClubHeader();
    $this->SetFont('Times','B',16);

    $this->Cell(0, 10, utf8_decode("Informations relatives aux inspecteurs TIV du ".$this->_date.""),0,1);
    $this->Ln(6);

    $db_query = "SELECT inspection_tiv.id_inspecteur_tiv, inspecteur_tiv.nom, inspecteur_tiv.numero_tiv, COUNT(inspection_tiv.id_inspecteur_tiv) \n".
                "FROM inspection_tiv,inspecteur_tiv \n".
                "WHERE date = '".$this->_date."' AND inspecteur_tiv.id = inspection_tiv.id_inspecteur_tiv \n".
                "GROUP BY inspection_tiv.id_inspecteur_tiv \n".
                "ORDER BY inspecteur_tiv.nom\n";
    $db_result = $this->_db_con->query($db_query);
    $header = array("id", "Nom inspecteur", "numéro TIV", "Nombre de bouteille à inspecter");
    $w = array(10, 55, 40, 0);
    $this->SetFillColor(127,127,127);
    for($i = 0; $i < count($header); $i++) {
      $this->Cell($w[$i], 10, utf8_decode($header[$i]), 1, 0, 'C', 1);
    }
    $this->Ln();
    $this->SetFont('Times','',13);
    while($result = $db_result->fetch_array()) {
      for($i = 0; $i < count($header); $i++)
        $this->Cell($w[$i],10,utf8_decode($result[$i]), 1, 0);
      $this->Ln();
    }
    $this->Ln(5);
    $this->Cell(0, 5,utf8_decode("Vous trouverez les fiches récapitulatives de chaque inspecteur TIV dans les pages suivantes."), 0, 1);
  }
  function addInspectionResume() {
    $this->SetFont('Times','B',16);
    $this->Cell(0, 10, utf8_decode("Informations relatives à l'inspection TIV du ".$this->_date.""),0,1);

    $this->SetFont('Times','',12);

    $db_query = "SELECT inspection_tiv.id, bloc.date_derniere_epreuve FROM inspection_tiv,bloc ".
                "WHERE date = '".$this->_date."' AND bloc.id = inspection_tiv.id_bloc" ;
    $db_result = $this->_db_con->query($db_query);
    $total = 0;
    $count_tiv = 0;
    $reepreuve = 0;
    $max_time_tiv = strtotime("-48 months", strtotime($this->_date));
    while($result = $db_result->fetch_array()) {
      $total++;
      $time = strtotime($result[1]);
      if($time > $max_time_tiv) $count_tiv++;
      else $reepreuve++;
    }
    $this->Cell(0,10,utf8_decode("Il est prévu d'inspecter $total blocs au total dont $reepreuve réépreuve(s) et ".$count_tiv." inspection(s) TIV."), 0, 1);
    $this->Cell(0,10,utf8_decode("Vous trouverez l'ensemble des fiches TIV dans les pages suivantes."), 0, 1);
  }
  function addResume() {
    $this->addInspecteurResume();
    $this->Ln(10);
    $this->addInspectionResume();
  }
  function addInspectionHeader($result) {
    // Charge template fiche inspection TIV
    $pageCount = $this->setSourceFile($this->_tiv_template["file"]);
    if($pageCount == 0) {
      print "Erreur d'ouverture du PDF ".$this->_tiv_template["file"];
      exit();
    }
    $template = $this->importPage(1, '/MediaBox');
    // Procede a l'affichage de la fiche
    $this->SetFont('Times', '', 13);
    $this->addPage('L');
    $this->useTemplate($template);
    foreach($this->_tiv_template["champ"] as $key => $value) {
      $this->SetXY($value[0], $value[1]);
      $this->MultiCell($value[2], $this->_tiv_template["interligne"], utf8_decode($value[3]), $this->_debug, 'R');
    }
    foreach($this->_tiv_template["result"] as $key => $value) {
      $this->SetXY($value[0], $value[1]);
      if($key === "adresse_tiv") {
        $result[$key] = str_replace('\n', "\n", preg_replace("/\s([0-9]{5})/", " \\n\\1", $result[$key]));
      }
      $this->MultiCell($value[2], $this->_tiv_template["interligne"], utf8_decode($result[$key]), $this->_debug, 'R');
    }
    foreach($this->_tiv_template["query"] as $key => $value) {
      $db_query = preg_replace("/ID_INSPECTEUR/", $result["id_inspecteur_tiv"], $value[3]);
      $db_count = $this->_db_con->query($db_query);
      $count = $db_count->fetch_array();
      $this->SetXY($value[0], $value[1]);
      $this->MultiCell($value[2], $this->_tiv_template["interligne"], utf8_decode($count[0]), $this->_debug, 'R');
    }
    $this->SetY($this->_tiv_template["start_info_bloc"][1]);
  }
  function addInspecteurFile() {
    $db_query = "SELECT DISTINCT id_inspecteur_tiv, inspecteur_tiv.nom, numero_tiv, adresse_tiv, telephone_tiv, id_inspecteur_tiv ".
                "FROM inspection_tiv, inspecteur_tiv ".
                "WHERE inspection_tiv.date = '".$this->_date."' AND id_inspecteur_tiv = inspecteur_tiv.id ".
                "GROUP BY inspection_tiv.id_inspecteur_tiv ORDER BY inspecteur_tiv.nom";
    $db_result = $this->_db_con->query($db_query);
    while($result = $db_result->fetch_array()) {
      $this->addInspectionHeader($result);
      $this->addInspecteurFileBlocsInformations($result);
    }
  }
  function addInspecteurFileBlocsInformationsTableHeader() {
    // Charge template pied de page inspection TIV
    $pageCount = $this->setSourceFile($this->_tiv_template["pied"]);
    if($pageCount == 0) {
      print "Erreur d'ouverture du PDF ".$this->_tiv_template["pied"];
      exit();
    }
    $pied = $this->importPage(1, '/MediaBox');
    $this->useTemplate($pied);
    // Init font + position
    $this->SetFont('Times', 'U', 10);
    $this->SetFillColor(255,255,255);
    $this->SetX($this->_tiv_template["start_info_bloc"][0]);
    // Stockage emplacement cadre
    $start_x = $x = $this->GetX();
    $start_y = $y = $this->GetY();
    $this->_bloc_header_width = 0;
    // Remplissage structure utilise pour l'affichage
    $this->_bloc_info_to_retrieve = array();
    // Lancement de l'affichage de l'entete
    foreach($this->_bloc_definition as $key => $value) {
      $interligne = $this->_tiv_template["interligne"] * 2;
      // Reduction taille hauteur si trop large ou si sous categorie
      if($this->GetStringWidth($key) > $value[0] || is_array($value[1])) {
        $interligne = $this->_tiv_template["interligne"];
      }
      $this->MultiCell($value[0], $interligne, utf8_decode($key), 1, 'C');
      // Cas d'une sous categorie => on parcourt le sous tableau
      if(is_array($value[1])) {
        $x_sub = $x;
        $y_sub = $this->GetY();
        $this->SetXY($x, $y_sub);
        foreach($value[1] as $subkey => $subvalue) {
          $this->MultiCell($subvalue[0], $interligne, utf8_decode($subkey), 1, 'C');
          $x_sub += $subvalue[0];
          $this->SetXY($x_sub, $y_sub);
          // Affichage
          $this->_bloc_info_to_retrieve[$subvalue[1]] = $subvalue[0];
        }
        $this->SetY($this->GetY() - $interligne);
      } else {
        // Preparation structure renvoyant les champs a afficher pour chaque bloc
        $this->_bloc_info_to_retrieve[$value[1]] = $value[0];
      }
      // Stockage pour affichage du cadre et repositionnement de la prochaine cellule
      $x += $value[0];
      $this->_bloc_header_width += $value[0];
      $this->SetXY($x, $y);
    }
    // Ajout d'un cadre
    $this->SetLineWidth(1);
    $this->Rect($start_x, $start_y, $this->_bloc_header_width, $interligne);
    $this->SetLineWidth(0.2);
    // Changement ligne
    $this->Ln();
  }
  function addInspecteurFileBlocsInformations($bloc_result) {
    $this->addInspecteurFileBlocsInformationsTableHeader();
    $interligne = $this->_tiv_template["interligne_bloc"];
    $start_y = $this->GetY();
    $this->SetFont('Times', '', 10);
    $to_retrieve = $this->_bloc_info_to_retrieve;
    $db_query = "SELECT ".implode(",", array_keys($to_retrieve))." ".
                "FROM inspection_tiv, bloc ".
                "WHERE inspection_tiv.date = '".$this->_date."' ".
                "AND id_inspecteur_tiv = ".$bloc_result[0]." AND bloc.id = id_bloc";
    $db_result = $this->_db_con->query($db_query);
    // Compteur pour savoir le nombre de ligne que nous pouvons créer
    $page_line_count = 1;
    // Nombre de ligne pour la premiere page
    $max_line_count  = $this->_tiv_template["first_page_tiv_count"];
    $height = 0;
    // Lancement affichage
    while($result = $db_result->fetch_array()) {
      $this->SetX($this->_tiv_template["start_info_bloc"][0]);
      $height += $interligne;
      foreach(array_keys($to_retrieve) as $elt) {
        $this->Cell($to_retrieve[$elt], $interligne, utf8_decode($result[$elt]), 1, 0, 'C');
      }
      // Changement de ligne
      $this->Ln();
      // Gestion regroupement des lignes
      if($page_line_count++ >= $max_line_count) {
        // Ajout du cadre courant
        $this->SetLineWidth(1);
        $this->Rect($this->_tiv_template["start_info_bloc"][0], $start_y, $this->_bloc_header_width, $height);
        $this->SetLineWidth(0.2);
        // Reinit variable affichage
        $height = 0;
        $page_line_count = 0;
        // Rajout de l'entete si on l'affiche tout le temps
        if($this->_tiv_template["always_show_tiv_info"]) {
          $this->addInspectionHeader($bloc_result);
          $this->addInspecteurFileBlocsInformationsTableHeader();
        } else {
          $max_line_count = $this->_tiv_template["max_tiv_count_per_page"];
          $this->AddPage('L');
          $this->addInspecteurFileBlocsInformationsTableHeader();
        }
        $start_y = $this->GetY();
        $this->SetFont('Times', '', 10);
      }
    }
    // Ajout d'un cadre
    $this->SetLineWidth(1);
    $this->Rect($this->_tiv_template["start_info_bloc"][0], $start_y, $this->_bloc_header_width, $height);
    $this->SetLineWidth(0.2);
  }
  function addBlocAlert($id_bloc) {
    $this->SetFont('Times', 'B', 14);
    $this->SetTextColor(255, 0, 0);
    $this->SetDrawColor(255, 0, 0);
    $db_query = "SELECT date_derniere_epreuve,date_dernier_tiv ".
                "FROM bloc ".
                "WHERE id = $id_bloc";
    $db_result = $this->_db_con->query($db_query);
    $result = $db_result->fetch_array();
    $date_epreuve = strtotime($result[0]);
    $date_prochaine_epreuve = strtotime("+5 years", $date_epreuve);
    $date_timestamp = strtotime($this->_date);
    if($date_epreuve < strtotime("-55 months", $date_timestamp)) {
      $this->SetXY(130, 21);
      $this->Cell(0, 8, utf8_decode("Réépreuve avant le ".date("d/m/Y", $date_prochaine_epreuve)), 1, 0, 'C');
    }
    $this->SetTextColor(0, 0, 0);
    $this->SetDrawColor(0, 0, 0);
  }
  function addBlocFile($id_bloc = false) {
    $bloc_condition = "1";
    if($id_bloc) $bloc_condition = "id_bloc = $id_bloc";
    $db_query = "SELECT inspection_tiv.id, id_bloc, inspecteur_tiv.numero_tiv, decision, inspecteur_tiv.nom ".
                "FROM inspection_tiv ".
                "LEFT JOIN inspecteur_tiv ON inspection_tiv.id_inspecteur_tiv = inspecteur_tiv.id ".
                "WHERE inspection_tiv.date = '".$this->_date."' AND $bloc_condition ".
                "ORDER BY inspecteur_tiv.nom";
    $db_result = $this->_db_con->query($db_query);
    while($result = $db_result->fetch_array()) {
      // Affichage de l'entête de la fiche (capacité du bloc, date des réépreuves etc.)
      $this->AddPage();
      $this->ClubHeader();
      $this->addBlocInformation($result[1]);
      // Ligne de séparation
      $this->Cell(0,5,"", 'B', 1, 1);
      $this->Ln(8);
      // Information concernant l'inspection TIV
      $this->SetFont('Times', 'B', 14);
      $this->Cell(45,10,utf8_decode("Vérificateur TIV n° "), 0, 0);
      $this->SetFont('Times',  '', 12);
      $this->Cell(max($this->GetStringWidth($result[2]), 30) + 2,8, $result[2], 1, 0);
      $this->Cell(3);
      // Affichage numéro fiche tiv
      $this->SetFont('Times', 'B', 14);
      $this->Cell(30,10,utf8_decode("Fiche TIV n° "), 0, 0);
      $this->SetFont('Times',  '', 12);
      $this->Cell(max($this->GetStringWidth($result[0]), 15) + 2,8, $result[0], 1, 1);
      foreach(array("exterieur", "interieur", "filetage", "robineterie") as $element)
        $this->addAspectInformation($result[0], $element);
      // Ligne de séparation
      $this->Cell(0,2,"", 'B', 1, 1);
      // Conclusion + signature
      $this->Ln(1);
      $this->SetFont('Times',  'B', 12);
      $this->Cell(30,8,"Conclusions : ", 0, 0);
      $this->Cell(17,10,utf8_decode($result[3]), 1, 0);
      $this->Cell(10);
      $this->Cell(30,8,"Signatures : ", 0, 0);
      $this->MultiCell(60,10,utf8_decode($result[4])."\n\n ", 1, 'C');
      // Affichage d'un message d'alerte en cas de dépassement de la date d'épreuve/tiv sur le bloc
      $this->addBlocAlert($result[1]);
    }
  }
  function addBlocInformation($id_bloc) {
    $db_query = "SELECT id, id_club, numero, capacite, date_premiere_epreuve, date_derniere_epreuve, ".
                "pression_service, pression_epreuve, constructeur, marque ".
                "FROM bloc ".
                "WHERE id ='$id_bloc'";
    $db_result = $this->_db_con->query($db_query);
    $bloc = $db_result->fetch_array();
    $this->SetFont('Times', 'B', 12);
    $this->Cell(30,10,utf8_decode("Bloc n° club "), 0, 0);
    $this->SetFont('Times',  '', 10);
    $this->Cell(8,8,$bloc["id_club"], 1, 0);
    $this->Cell(5);
    $this->SetFont('Times', 'B', 12);
    $this->Cell(50,10,utf8_decode("Numéro du constructeur"), 0, 0);
    $this->SetFont('Times',  '', 10);
    $this->Cell(25,8,$bloc["numero"], 1, 1);
    $this->SetFont('Times', 'B', 12);
    $this->Cell(35,8,"Constructeur :", 0, 0);
    $this->SetFont('Times',  '', 10);
    $this->Cell(40,8,$bloc["constructeur"], 0, 0);
    $this->SetFont('Times', 'B', 12);
    $this->Cell(25,8,"Marque :", 0, 0);
    $this->SetFont('Times',  '', 10);
    $this->Cell(25,8,$bloc["marque"], 0, 1);
    $this->Cell(25,8,utf8_decode("Capacité (litres) : ".$bloc["capacite"]." - Pression service : ".$bloc["pression_service"]." bars - ".
                                "Pression épreuve : ".$bloc["pression_epreuve"]." bars"), 0, 1);
    $this->Cell(25,8,utf8_decode("Première épreuve : ".$bloc["date_premiere_epreuve"]), 0, 0);
    $this->Cell(32);
    $this->Cell(25,8,utf8_decode("Dernière épreuve : ".$bloc["date_derniere_epreuve"]), 0, 0);
    $this->Cell(32);
    $prochaine_epreuve = date("Y-m-d", strtotime("+5 years", strtotime($bloc["date_derniere_epreuve"])));
    $this->Cell(25,8,utf8_decode("Prochaine épreuve : ".$prochaine_epreuve), 0, 1);
  }
  function addAspectInformation($id_inspection, $element) {
    $labels = array("interieur" => "intérieur", "exterieur" => "extérieur");
    $label = $element;
    if(array_key_exists($element, $labels)) $label = $labels[$element];

    $db_query = "SELECT id, etat_$element, remarque_$element ".
                "FROM inspection_tiv ".
                "WHERE id ='$id_inspection'";
    $db_result = $this->_db_con->query($db_query);
    $inspection = $db_result->fetch_array();
    $status = inspection_tivElement::getPossibleStatus($element == "interieur");
    $this->SetFont('Times', 'BU', 12);
    $this->Ln(8);
    $this->Cell(33, 8, utf8_decode("État $label :"), 0, 0);
    $this->SetFont('Times', 'B', 12);
    foreach($status as $state) {
      if(strlen($state) == 0) continue;
      $len = $this->GetStringWidth($state) + 2;
      $this->Cell($len, 8, utf8_decode($state), 0, 0);
      $this->Cell(5, 5, ($inspection["etat_$element"] == $state ? "X" : ""), 1, 0);
      $this->Cell(5);
    }
    $this->Cell(5, 7, "", 0, 1);
    $this->Cell(0,8,utf8_decode("Commentaire et action si état autre que bon :"), 0, 1);
    $this->MultiCell(0, 8, ($inspection["remarque_$element"] ? utf8_decode($inspection["remarque_$element"]) : "\n "), 1, 1);
  }
}

?>