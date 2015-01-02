<?php

namespace Controllers;

use Models\Artist;
use Models\Track;

class BaseController implements Controller
{

    private static $actions = array(
        'autocomplete',
        'recherche',
        'getArtistPage'
    );

    public function autocomplete()
    {
        try {
            $array = array();

            $res = Track::findByNameLike($_GET["term"], 5);
            foreach ($res as $trackId => $track) {
                $data = array(
                    'id' => $trackId,
                    'name' => $track->getTitle(),
                    'image' => $track->getArtistId()
                );
                $array[] = array("category" => "Musique", "label" => $track->getTitle(), "data" => $data);
            }

            $res = Artist::findByNameLike($_GET["term"], 5);
            foreach ($res as $artistId => $artist) {
                $data = array(
                    'id' => $artistId,
                    'name' => $artist->getName(),
                    'image' => $artist->getImageUrl()
                );
                $array[] = array("category" => "Artiste", "label" => $artist->getName(), "data" => $data);
            }
            echo json_encode($array);
        } catch (\PDOException $e) {
            echo json_encode(array("errors" => "databaseDown"));
        }
    }

    public function recherche()
    {

        $data = array();
        $tracks = Track::findByNameLikeWithArtist($_GET["q"],0);
        $data['musiques'] = array();
        $data['artistes'] = array();
        foreach ($tracks as $id => $track) {
            $arrayT = array(
                "track_id" => $track->getTrackId(),
                "title" => $track->getTitle(),
                "mp3_url" => $track->getMp3Url(),
                "artist_id" => $track->getArtistId(),
                "name" => $track->getArtist()->getName(),
                "image_url" => $track->getArtist()->getImageUrl()
            );
            //array_push($data["musiques"], $arrayT);
            $data["musiques"][] = $arrayT;
        }
        $artists = Artist::findByNameLike($_GET["q"], 0);
        foreach ($artists as $artistId => $artist) {
            $arrayA = array(
                'artist_id' => $artistId,
                'name' => $artist->getName(),
                'image_url' => $artist->getImageUrl()
            );
            //array_push($data["artistes"], $arrayA);
            $data["artistes"][] =$arrayA;
        }
        echo json_encode($data);
    }
    
    public function getArtistPage()
    {
        $data = array();
        
        $artist = Artist::findByID($_GET["id"]);
        $tracks = Track::findByArtistID($_GET["id"]);
        
        $data["artiste"] = array();
        $data["musiques"] = array();
        
        foreach ($tracks as $id => $track) {
            $arrayT = array(
                "track_id" => $track->getTrackId(),
                "title" => $track->getTitle(),
                "mp3_url" => $track->getMp3Url(),
                "artist_id" => $track->getArtistId(),
            );
            array_push($data["musiques"], $arrayT);
        }
   
        $arrayA = array( "artist_id" => $artist->getId(), "name" => $artist->getName(), "image_url" => $artist->getImageUrl(), "info" => $artist->getInfo());
        //array_push($data["artiste"], $arrayA);
        $data["artiste"] = $arrayA;
        
        echo json_encode($data);
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