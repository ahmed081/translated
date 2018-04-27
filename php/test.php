<?php

require_once 'Classes/PHPExcel.php';

//require_once 'Classes/PHPExcel/IOFactory.php';

 

if(isset($_POST['excel'])) {

    // création des objets de base et initialisation des informations d'entête

    $classeur = new PHPExcel;

    $classeur->getProperties()->setCreator("Annie Gagnon");

    $classeur->setActiveSheetIndex(0);

    $feuille=$classeur->getActiveSheet();

 

    // ajout des données dans la feuille de calcul

    $feuille->setTitle('Nom affiché dans l\'onglet');
$feuille->setCellValueByColumnAndRow(0, 1, 'Les colonnes débutent à 0 et les lignes débutent à 1');
    

    $feuille->SetCellValue('A2', 'Il est aussi possible d\'utiliser la notation LettreChiffre (ex : A2)');

 

    // envoi du fichier au navigateur

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); 

    header('Content-Disposition: attachment;filename="nomfichier.xlsx"'); 

    header('Cache-Control: max-age=0'); 

    $writer = PHPExcel_IOFactory::createWriter($classeur, 'Excel2007'); 

    $writer->save('php://output');

}

else {

    // on envoie de l'information à l'écran seulement si le bouton de génération n'a pas été cliqué

    //require 'include/entete.inc.php';

 

    // affichage des données à l'écran

    

 

    // bouton qui permettra de générer le chiffrier Excel

    echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '">';

    echo '<input type="submit" value="Exporter vers Excel" name="excel" />';

    echo '</form>';

}	
?>