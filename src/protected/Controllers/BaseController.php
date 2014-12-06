<?php

namespace Controllers;

class BaseController implements Controller
{

    private static $actions = array(
        'test',
        'autocomplete'
    );
    
    public function test()
    {
        echo "ceci est un test";
    }
    
    public function autocomplete()
    {
        $connexion = ConnexionGiver::getConnexion();
        $bdd = ConnexionGiver::getDatabase();
        
        $query = "select nom from ( select title as 'nom' from tracks where title like '%" . $_GET["term"] . 
        "%' union select name as 'nom' from artists where name like '%" . $_GET["term"] . "%' ) as temp order by nom limit 5";
        $request = mysql_query($query);
        $res = mysql_fetch_assoc($request);

        $array = array();
        
        while($res != false)
        {
            array_push($array, $res["nom"]);
            $res = mysql_fetch_assoc($request);
        }

    
        echo json_encode($array);
    }
    
    public function recherche()
    {
        //var_dump($_GET);
        $connexion = ConnexionGiver::getConnexion();
        $bdd = ConnexionGiver::getDatabase();
        
        $queryT = "select tracks.*, artists.name, artists.image_url from tracks inner join artists on tracks.artist_id = artists.artist_id where tracks.title like '%" . $_GET["q"] . "%' or artists.name like '%" . $_GET["q"] . "%' order by title";
        $queryA = "select artists.* from artists where name like '%" . $_GET["q"] . "%' order by name";
        
        $requestT = mysql_query($queryT);
        $resT = mysql_fetch_assoc($requestT);
        
        $requestA = mysql_query($queryA);
        $resA = mysql_fetch_assoc($requestA);
        
        $arrayT = array();
        $arrayA = array();
        
        while($resT != false)
        {
            array_push($arrayT, $resT);
            $resT = mysql_fetch_assoc($requestT);
        }
        
        while($resA != false)
        {
            array_push($arrayA, $resA);
            $resA = mysql_fetch_assoc($requestA);
        }
        
        $retour = array();
        $retour["musiques"] = $arrayT;
        $retour["artistes"] = $arrayA;
        
        
        echo json_encode($retour);
    }
    
    public function index()
    {
        
    }

    public function hasAction($action)
    {
        return in_array($action, self::$actions);
    }

    public function hasRightAccess($action)
    {
        return true;
    }

    public function accessDenied()
    {

    }
} 