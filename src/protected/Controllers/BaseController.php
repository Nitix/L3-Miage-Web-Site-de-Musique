<?php

namespace Controllers;

use Models\Artist;
use Models\Track;

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
        try{
            $array = array();

            $res = Track::findByNameLike($_GET["term"], 5);
            foreach($res as $trackId => $track)
            {
                $data = array(
                    'id' => $trackId,
                    'name' => $track->getTitle(),
                    'image' => $track->getArtistId()
                    );
                $array[] = array("category" => "Musique", "label" => $track->getTitle(), "data" => $data);
            }

            $res = Artist::findByNameLike($_GET["term"], 5);
            foreach($res as $artistId => $artist)
            {
                $data = array(
                    'id' => $artistId,
                    'name' => $artist->getName(),
                    'image' => $artist->getImageUrl()
                );
                $array[] = array("category" => "Artiste", "label" => $artist->getName(), "data" => $data);
            }
            echo json_encode($array);
        }catch (\PDOException $e){
            echo json_encode(array("errors" => "databaseDown"));
        }
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