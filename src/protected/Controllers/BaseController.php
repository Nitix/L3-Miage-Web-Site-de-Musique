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
        $connexion = mysql_connect("romainpapelier.fr.mysql","romainpapelier_","ixktis");
        $bdd = mysql_select_db("romainpapelier_", $connexion);
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