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

switch ($_GET['method']) {
	case '1':
		 Get_translate($db);
		break;
	case '2':
		 Get_Translated($db);
		break;
	case '3':
		 exportSQL($db);
		break;
	case '4':
		 exportExcel($db);
		break;
	case '5':
		 importer($db);
		break;

}

$db=null;




function insert($db)
{
	$objs =  Get_Translated($db);
	$user_id=(int)$_POST['id_user'];

	foreach ($objs as $obj) {
			echo "$key <br>";
				try{

	/*				if(!empty($obj['fr']))
						$d=$database->exec("INSERT INTO traduction ( id_traducteur, id_langue, translate , id_mot, modifier, valider, date_traduction, date_modification) VALUES ( $id_user ,'1', '$obj['fr']', $obj['id'], 'false', 'false', CURRENT_DATE, NULL);");
					if(!empty($obj['ar']))
						$d=$database->exec("INSERT INTO traduction ( id_traducteur, id_langue, translate , id_mot, modifier, valider, date_traduction, date_modification) VALUES ( $id_user ,'2', '$obj['ar']', $obj['id'], 'false', 'false', CURRENT_DATE, NULL);");
					if(!empty($obj['es']))
						$d=$database->exec("INSERT INTO traduction ( id_traducteur, id_langue, translate , id_mot, modifier, valider, date_traduction, date_modification) VALUES ( $id_user ,'3', '$obj['es']', $obj['id'], 'false', 'false', CURRENT_DATE, NULL);");*/
				}
		        catch(PDOException $a)
		        {
		           echo $a->getMessage();
		           die();
		        }
		
	}
}

function update($db)
{
	$objs =  Get_Translated($db);
	$user_id=(int)$_POST['id_user'];

	foreach ($objs as $obj) {

			echo "$key <br>";
				try{

	/*				if(!empty($obj['fr']))
						$d=$database->exec("UPDATE traduction SET translate =$obj['fr'] ,modifier='TRUE'  WHERE id=$user_id;");
					if(!empty($obj['ar']))
						$d=$database->exec("UPDATE traduction SET translate =$obj['ar'] ,modifier='TRUE'  WHERE id=$user_id;");
					if(!empty($obj['es']))
						$d=$database->exec("UPDATE traduction SET translate =$obj['ar'] ,modifier='TRUE'  WHERE id=$user_id;");*/
				}
		        catch(PDOException $a)
		        {
		           echo $a->getMessage();
		           die();
		        }
		
	}
}




function exportSQL($db)
{

	$tables = array();
	$result = $db->query('SHOW TABLES');

	while($row = $result->fetch())
	{
		$tables[] = $row[0];
	}

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
	

	$handle = fopen('db-backup.sql','w');
	fwrite($handle,$return);
	fclose($handle);
	header("Content-disposition: attachment; filename=db-backup.sql");
	header('Content-Type: application/force-download');
	header('Content-Transfer-Encoding: binary');
	header("Content-Length: " . filesize('db-backup.sql'));
	readfile('db-backup.sql');
}

function exportExcel($db)
{
	require_once '../include/Classes/PHPExcel.php';
	require_once '../include/Classes/PHPExcel/IOFactory.php';


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

					$j++;
					

				}
				
				
				$j=0;
				$t++;
			}
			$t=2;

			$objPHPExcel->getActiveSheet()->setTitle($table);

			$i++;

		}

		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="name_of_file.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
}

function Get_translate($db)
{
				$user_id=(int)$_POST['id_user'];
        		$page =(int)$_POST['page'];
        		$count_par_page = (int)$_POST['count_par_page'];
                $start = ($count_par_page*($page - 1));
                $mot =  array(
                    'id' => '',
                    'source' => '',
                    'fr' => '',
                    'ar' => '',
                    'es' => ''
                );
				$query =$db->query("SELECT count(id) as nbr FROM  mots  where  id not in (SELECT id_mot from traduction )");
				$nbr = $query->fetch(PDO::FETCH_OBJ);
				

                $mots  = array();
                array_push($mots,$nbr);
                $query=$db->query("select m.id,m.mot from mots m  where m.id not in (SELECT id_mot from traduction ) LIMIT $count_par_page offset $start");
                $res = $query->fetchAll(PDO::FETCH_OBJ);
                
                foreach ($res as $var) {
                    $id=$var->id;
                    $word=$var->mot;
                    $mot['id']=$id;
                    $mot['source']=$word;

                                       
                    array_push($mots,$mot);
                   
                }
                
                echo json_encode($mots);
}

function Get_Translated($db)
{


				$user_id=(int)$_POST['id_user'];
        		$page =(int)$_POST['page'];
        		$count_par_page = (int)$_POST['count_par_page'];
					//$user_id=1;
	               //$page=1;
	               // $count_par_page=6;
	                //$start = ($count_par_page*($page - 1));

                $mot =  array(
                    'id' => '',
                    'source' => '',
                    'fr' => '',
                    'ar' => '',
                    'es' => ''
                );
				$query =$db->query("SELECT count(id) as nbr FROM 	traduction where id_traducteur=$user_id");
				$nbr = $query->fetch(PDO::FETCH_OBJ);
				

                $mots  = array();
                array_push($mots,$nbr);
                $query=$db->query("select m.id,m.mot, COUNT(t.id) as nbr from traduction t , mots m ,  langue l where m.id=t.id_mot and  l.id=t.id_langue AND t.id_traducteur = $user_id GROUP BY m.id LIMIT $count_par_page offset $start");
                $res = $query->fetchAll(PDO::FETCH_OBJ);
                
                foreach ($res as $var) {
                    $id=$var->id;
                    $word=$var->mot;
                    $mot['id']=$id;
                    $mot['source']=$word;

                    $query=$db->query("select m.id,m.mot , t.translate ,l.langu from traduction t , mots m , langue l where m.id=t.id_mot and l.id=t.id_langue AND t.id_traducteur = $user_id and m.id = $id");
                    $langues=$query->fetchAll(PDO::FETCH_OBJ);
                    
                    foreach ($langues as $key) {
                        if($key->langu == 'francais')
                        {
                            $mot['fr']=$key->translate ;

                        }
                        else
                        if($key->langu == 'arabe')
                        {
                            $mot['ar']=$key->translate ;

                        }else
                        if($key->langu == 'espaniol')
                        {
                            $mot['es']=$key->translate ;
                        }
                        
                    }
                    
                    array_push($mots,$mot);
                    $mot =  array(
                    'id' => '',
                    'source' => '',
                    'fr' => '',
                    'ar' => ''
                    ,'es' => ''
                        );

                }


                $id=1;

               // $query=$db->query("");

               // $res = $query->fetchAll(PDO::FETCH_OBJ);
                
               echo json_encode($mots);
                //return $mots;
}



function importer()
{
		$id_user= $_GET['id_user'];
		$typefile = $_GET['type'];
		$langue=$_GET['langue'];
		$path = "../uploads/";
		$path = $path . basename( $_FILES['file']['name']);
		move_uploaded_file($_FILES['file']['tmp_name'], $path);
		switch ($typefile) {
			case 'xlf':
				$xml=simplexml_load_file("../uploads/".$_FILES['file']['name']) or die("Error: Cannot create object");
				//xmlFile($xml ,$db , $id_user , $langue);
				break;		
			case 'xml':
				$xml=simplexml_load_file("../uploads/".$_FILES['file']['name']) or die("Error: Cannot create object");	
				//mobileFile($xml ,$db , $id_user , $langue);
				break;
			case 'js':
				$string = file_get_contents("../uploads/".$_FILES['file']['name']);$json_a= json_decode($string , true);	
				//jsonFile($json_a ,$db , $id_user , $langue);
				break;
		}
}




function xmlFile($xml , $database, $id_user , $langue)
{
	foreach ($xml as $file) {
		foreach ($file as $body) {
			
			foreach ($body as $a) {
				$id= (int)$a['id'];
				echo $a->source;
				echo $a->target;
				echo "<br>";
				try{
					$database->exec("INSERT INTO mots (id, mot , id_admin  , id_correcteur , default_traduction , source_type , field , couriger , traduite , valider , date_creation , date_modification , supprimer) values ( $id , '$a->source' , '1' , NULL , NULL , NULL ,NULL, 'false' ,'true' , 'false' , CURRENT_DATE, CURRENT_DATE , 'false' )");

					$d=$database->exec("INSERT INTO traduction ( id_traducteur, id_langue, translate , id_mot, modifier, valider, date_traduction, date_modification) VALUES ( $id_user ,'$langue', '$a->target', $id, 'false', 'false', CURRENT_DATE, NULL);");
				}
		        catch(PDOException $a)
		        {
		           echo $a->getMessage();
		           die();
		        }
			}

		}
	}
}
function mobileFile($xml , $database, $id_user , $langue)
{
	$query =$database->query("SELECT max(id) as id FROM mots");
	$MaxId = $query->fetch(); 
	$MaxId=(int)$MaxId['id'];
	$id=$MaxId+1;
	
	foreach ($xml as $mot) {

		$word = $mot['name'];
		$translate = $mot;
		echo $id.$word."-->".$translate."<br>";
						try{
					$database->exec("INSERT INTO mots (id, mot , id_admin  , id_correcteur , default_traduction , source_type , field , couriger , traduite , valider , date_creation , date_modification , supprimer) values ( $id , '$word' , '1' , NULL , NULL , NULL ,NULL, 'false' ,'true' , 'false' , CURRENT_DATE, CURRENT_DATE , 'false' )");

					$d=$database->exec("INSERT INTO traduction ( id_traducteur, id_langue, translate , id_mot, modifier, valider, date_traduction, date_modification) VALUES ( $id_user ,'$langue', '$translate', $id, 'false', 'false', CURRENT_DATE, NULL);");
					
				}
		        catch(PDOException $a)
		        {
		           echo $a->getMessage();
		           die();
		        }
		        $id=$id+1;

	}
}
function jsonFile($json_a , $database, $id_user , $langue)
{
	$query =$database->query("SELECT max(id) as id FROM mots");
	$MaxId = $query->fetch(); 
	$MaxId=(int)$MaxId['id'];
	$id=$MaxId+1;

	//echo json_encode($string);
	 foreach ($json_a as $key) {
	 	foreach ($key as $value => $a) {
	 		echo $value ;
	 		foreach ($a as $mot => $translate) {
	 			echo $mot."-->".$translate."<br>";
						try{
					$database->exec("INSERT INTO mots (id, mot , id_admin  , id_correcteur , default_traduction , source_type , field , couriger , traduite , valider , date_creation , date_modification , supprimer) values ( $id , '$mot' , '1' , NULL , NULL , NULL ,NULL, 'false' ,'true' , 'false' , CURRENT_DATE, CURRENT_DATE , 'false' )");

					$d=$database->exec("INSERT INTO traduction ( id_traducteur, id_langue, translate , id_mot, modifier, valider, date_traduction, date_modification) VALUES ( $id_user ,'$langue', '$translate', $id, 'false', 'false', CURRENT_DATE, NULL);");
					
				}
		        catch(PDOException $a)
		        {
		           echo $a->getMessage();
		           die();
		        }
		        $id=$id+1;
	 		}
	 		echo "<br>";
	 	}
	 }
}

//jsonFile($json_a ,$db , $id_user , $langue);
?>

















		







