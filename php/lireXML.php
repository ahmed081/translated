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










function nbr_traduction($db)
{
	$id = $_POST['id_user'];
	$id=(int)$id;
		$query =$db->query("SELECT count(id) as nbr FROM 	traduction where id_traducteur=$id");
		$nbr = $query->fetch(PDO::FETCH_OBJ);
		echo json_encode($nbr);

}

function Get_Translate($db)
{
				$user_id=(int)$_GET['id_user'];
        		$page =(int)$_GET['page'];
        		$count_par_page = (int)$_GET['count_par_page'];
                $start = ($count_par_page*($page - 1));
                $mot =  array(
                    'id' => '',
                    'source' => '',
                    'fr' => '',
                    'ar' => '',
                    'es' => ''
                );

                $mots  = array();
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
                    'ar' => '',
                    'es' => ''
                        );

                }


                $id=1;

               // $query=$db->query("");

               // $res = $query->fetchAll(PDO::FETCH_OBJ);
                
                echo json_encode($mots);
}

function UploadFile($db)
{
		$id_user= $_GET['id_user'];
		$typefile = $_GET['type'];
		$langue=$_GET['langue'];

		$path = "uploads/";
		$path = $path . basename( $_FILES['file']['name']);


		move_uploaded_file($_FILES['file']['tmp_name'], $path);

		switch ($typefile) {
			case 'xlf':
				$xml=simplexml_load_file("uploads/".$_FILES['file']['name']) or die("Error: Cannot create object");
				
				//xmlFile($xml ,$db , $id_user , $langue);

				break;
				
			case 'xml':
				$xml=simplexml_load_file("uploads/".$_FILES['file']['name']) or die("Error: Cannot create object");
				
				//mobileFile($xml ,$db , $id_user , $langue);
				break;
			case 'js':
				$string = file_get_contents("uploads/".$_FILES['file']['name']);$json_a= json_decode($string , true);
				
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

















		







