<?php
        try
        {
            $db = new PDO('mysql:host=localhost;dbname=traduction' , 'root' , '');
        }
         catch(PDOException $a)
        {
           echo $a->getMessage();
           die();
        }
/*if(isset($_POST['excel'])) {


require_once 'Classes/PHPExcel.php';
require_once 'Classes/PHPExcel/IOFactory.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$i=0;
$j=0;
$t=2;
		$tables = array();
		$result = $db->query('SHOW TABLES');

		while($row = $result->fetch())
		{
			$tables[] = $row[0];
		}
// Create a first sheet, representing sales data
		foreach ($tables as $table) 
		{
			
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($i);

			$result = $db->query('SELECT * From ' .$table);
			$result = $result->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as $key => $value) {
				
				foreach ($value as $k => $v) {

					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, 1, $k);
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $t, $v);
					//print_r($value);
					$j++;
					
					//break;
				}
				
				
				$j=0;
				$t++;
			}
			$t=2;
			
			
			//$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Something');

		// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle($table);

// Create a new worksheet, after the default sheet

			$i++;

}
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="name_of_file.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

}

else {

    // on envoie de l'information à l'écran seulement si le bouton de génération n'a pas été cliqué

    //require 'include/entete.inc.php';

 

    // affichage des données à l'écran

    

 

    // bouton qui permettra de générer le chiffrier Excel

    echo "<form method='post' action=''>";

    echo '<input type="submit" value="Exporter vers Excel" name="excel" />';

    echo '</form>';

}*/	





backup_tables($db);

/* backup the db OR just a table */
function backup_tables($db)
{
	
	
	
	
	//get all of the tables
	
	
		$tables = array();
		$result = $db->query('SHOW TABLES');

		while($row = $result->fetch())
		{
			$tables[] = $row[0];
		}

	
	//cycle through
	$return='';	
	foreach($tables as $table)
	{
		$result = $db->query('SELECT * FROM '.$table);
		$num_fields = $result->columnCount();;
		
		
		$row2 = $db->query('SHOW CREATE TABLE '.$table);
		$row2=$row2->fetch();
		$return.= "\r\n".$row2[1].";\r\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = $result->fetch())
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j < $num_fields; $j++)     
				{
					$row[$j] = addslashes($row[$j]);
					//$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j < ($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\r\n";
			}
		}
		$return.="\r\n";
	}
	
	//save file
	$handle = fopen('db-backup.sql','w');
	fwrite($handle,$return);
	fclose($handle);
	//
	header("Content-disposition: attachment; filename=db-backup.sql");
	header('Content-Type: application/force-download');
	header('Content-Transfer-Encoding: binary');
	header("Content-Length: " . filesize('db-backup.sql'));
	readfile('db-backup.sql');

}
$db=null;


?>