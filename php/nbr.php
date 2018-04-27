<?php
$id = $_GET['id_user'];
$id=(int)$id;
        try{
            $db = new PDO('mysql:host=localhost;dbname=traduction' , 'root' , '');
        }
        
        
         catch(PDOException $a)
        {
           echo $a->getMessage();
           die();
        }


	$query =$db->query("SELECT count(id) as nbr FROM 	traduction where id_traducteur=$id");
	$nbr = $query->fetch(PDO::FETCH_OBJ);
	echo json_encode($nbr);

?>