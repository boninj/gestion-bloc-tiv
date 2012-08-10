<?php
$bloc_elements = array(
  "id",
  "id_club",
  "nom_proprietaire",
  "adresse",
  "constructeur",
  "marque",
  "numero",
  "capacite",
  "date_premiere_epreuve",
  "date_derniere_epreuve",
  "date_dernier_tiv",
  "pression_service"
);

$bloc_rules = '
  debug: true,
  rules: {
    club_id: {
        required: true,
    },
    nom_proprietaire: {
        required: true,
    },
    adresse: {
        required: true,
    },
    constructeur: {
        required: true,
    },
    marque: {
        required: true,
    },
    numero: {
        required: true,
    },
    capacite: {
        required: true,
    },
    date_premiere_epreuve: {
        required: true,
        date: true
    },
    date_derniere_epreuve: {
        required: true,
        date: true
    },
    date_dernier_tiv: {
        required: true,
        date: true
    },
    pression_service: {
        required: true,
        number: true
    },
  }';

$bloc_capacite = array("6", "10", "12 long", "12 court", "15");
$bloc_pression = array("150", "176", "200", "232", "300");
$bloc_forms = array(
  "id_club"               => array("required", "number", "Référence du bloc au sein du club"),
  "nom_proprietaire"      => array("required", false,    "Nom du propriétaire du bloc"),
  "adresse"               => array("required", false,    "Adresse du propriétaire du bloc"),
  "constructeur"          => array("required", false,    "Constructeur du bloc (ex : ROTH)"),
  "marque"                => array("required", false,    "Marque du bloc (ex : Aqualung)"),
  "numero"                => array("required", false,    "Numéro de constructeur du bloc"),
  "capacite"              => array("required", $bloc_capacite,    "Capacité du bloc"),
  "date_premiere_epreuve" => array("required", "date",   "Date de la première épreuve du bloc"),
  "date_derniere_epreuve" => array("required", "date",   "Date de la dernière épreuve du bloc (tous les 5 ans)"),
  "date_dernier_tiv"      => array("required", "date",   "Date de la dernière inspection visuelle (tous les ans)"),
  "pression_service"      => array("required", $bloc_pression, "Pression de service du bloc (ex : 200 bars)"),
);

$detendeur_elements = array(
  "id",
  "modele",
  "id_1ier_etage",
  "id_2e_etage",
  "id_octopus",
  "date",
);
$detendeur_rules = '
  debug: true,
  rules: {
    modele: {
        required: true,
    },
    id_1ier_etage: {
        required: true,
    },
    id_2e_etage: {
        required: true,
    },
    id_octopus: {
        required: true,
    },
    date: {
        required: true,
        date: true
    },
  }';

$detendeur_forms = array(
  "modele"         => array("required", "number", "Modèle de détendeur"),
  "id_1ier_etage"  => array("required", false,    "Référence constructeur du 1ier étage"),
  "id_2e_etage"    => array("required", false,    "Référence constructeur du 2ieme étage"),
  "id_octopus"     => array("required", false,    "Référence constructeur de l'octopus"),
  "date"           => array("required", "date",   "Date de construction du détendeur"),
);

$stab_elements = array(
  "id",
  "modele",
  "taille",
);

$stab_rules = '
  debug: true,
  rules: {
    modele: {
        required: true,
    },
    taille: {
        required: true,
    },
  }';

$stab_taille = array("junior", "XS", "S", "M", "M/L", "L", "XL", "XXL");
$stab_forms = array(
  "modele"       => array("required", "number",      "Modèle de stab"),
  "taille"       => array("required", $stab_taille , "Taille de la stab"),
);

$inspecteur_tiv_elements = array(
  "id",
  "nom",
  "numero_tiv",
  "adresse_tiv",
  "telephone_tiv",
);

$inspecteur_tiv_rules = '
  debug: true,
  rules: {
    nom: {
        required: true,
    },
    numero_tiv: {
        required: true,
    },
    adresse_tiv: {
        required: true,
    },
    telephone_tiv: {
        required: true,
    },
  }';

$inspecteur_tiv_forms = array(
  "nom"           => array("required", "text",      "Nom de l'inspecteur TIV"),
  "numero_tiv"    => array("required", "text", "Numéro de TIV de l'inspecteur"),
  "adresse_tiv"   => array("required", "text", "Adresse du TIV"),
  "telephone_tiv" => array("required", "text", "Téléphone du TIV"),
);

function get_columns_from_element($element) {
  $array = $element."_elements";
  global $$array;
  return $$array;
}

?>