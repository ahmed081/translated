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
        $user_id=(int)$_GET['id_user'];
        $page =(int)$_GET['page'];
        $count_par_page = (int)$_GET['count_par_page'];


        function Get_Translate($user_id , $page , $count_par_page ,$db)
        {
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


Get_Translate($user_id , $page , $count_par_page , $db);



?>