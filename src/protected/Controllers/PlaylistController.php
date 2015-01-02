<?php


namespace Controllers;

use Models\Playlist;
use Models\PlaylistTrack;
use Models\Track;
use Models\User;


/**
 * Control the interaction with the user
 * @package Controllers\User
 */
class PlaylistController implements Controller
{

    /**
     * @var array List of actions that can do the client
     */
    private $actions = array(
        'getPlaylists',
        'addPlaylist',
        'getPlaylist',
        'delPlaylist',
        'addTrackToPlaylist',
        'delTrackFromPlaylist',
    );

    /**
     * Call the default page of the controller
     */
    function index()
    {
    }
    
    function getPlaylists()
    {
        $array = array();
       
       //si la liste des playlists n'existe pas on l'a créé
        if(isset($_SESSION["playlists"]))
        {
            $array = $_SESSION["playlists"];
        }
        
        echo json_encode($array);
    }
    
    function getPlaylist()
    {
        $trouve = false;
        
        //si la liste des playlists n'existe pas on l'a créé
        if(isset($_SESSION["playlists"]))
        {
            $array = $_SESSION["playlists"];
        }
        
        //on cherche la playlist par son id
        foreach($_SESSION["playlists"] as $plnum => $pl)
        {
            
            if($pl["playlist_id"] == $_GET["id"])
            {
                $trouve = true;
                echo json_encode($pl);
                break;
            }
        }
        
        if(!$trouve)
        {
            echo json_encode(false);
        }
    }
    
    function addPlaylist()
    {
        //si la liste des playlists n'existe pas, on la créé
        if(!isset($_SESSION["playlists"]))
        {
            $_SESSION["playlists"] = array();
        }
        
        $alreadyExists = false;
        
        //on cherche si il n'existe pas deja une playlist de ce nom
        foreach($_SESSION["playlists"] as $plnum => $pl)
        {
            
            if($pl["playlist_name"] == $_GET["newPlaylistName"])
            {
                $alreadyExists = true;
                break;
            }
        }
        
        //si une playlist avec ce nom existe deja, ajout impossible
        if($alreadyExists)
        {
            echo json_encode(false);
        }
        else
        {
            //sinon,
            $npl = array();
            
            //si la il n'y avait aucune playlist avant, on créé la premiere
            if(count($_SESSION["playlists"]) == 0)
            {
                $id = 0;
                $_SESSION["playlists_lastid"] = 0;
            }
            else
            {
                //sinon on en créé une nouvelle avec un ID auto incrementé
                $_SESSION["playlists_lastid"] = $_SESSION["playlists_lastid"] + 1;
                $id = $_SESSION["playlists_lastid"];
            }
            
            //on créé la nouvelle playlist
            $npl["playlist_id"] = $id;
            $npl["playlist_name"] = $_GET["newPlaylistName"];
            
            //puis on ajoute la nouvelle playlist
            $_SESSION["playlists"][] = $npl;
            
            //si l'utilisateur est loggé, on enregistre la modification en base
            if(isset($_SESSION["user"]))
            {
                //ajouter la playlist en base
            }
            
            //on retourne la nouvelle liste de playlist obtenue
            echo $this->getPlaylists();
        }
        
    }
    
    function delPlaylist()
    {

        $tmp = array();
        $res = false;
        
        foreach($_SESSION["playlists"] as $plnum => $pl)
        {
            if($pl["playlist_id"] == $_GET["id"])
            {
                if(isset($_SESSION["user"]))
                {
                    //supprimer la playlist en base
                }
                $res = true;
            }
            else
            {
                $tmp[] = $pl;
            }
        }
        
        //unset($_SESSION["playlists"]);
        $_SESSION["playlists"] = array();
        
        foreach($tmp as $plnum => $pl)
        {
            $_SESSION["playlists"][] = $pl;
        }
        
        echo json_encode($res);
    }
    
    function addTrackToPlaylist()
    {
        $track_id = $_GET["trid"];
        $playlist_id = $_GET["plid"];
        
        foreach($_SESSION["playlists"] as $plnum => $pl)
        {
            if($pl["playlist_id"] == $playlist_id)
            {
                if(!isset($_SESSION["playlists"][$plnum]["tracks"]))
                {
                    $_SESSION["playlists"][$plnum]["tracks"] = array();
                }
                
                $track = array();
                $track["track_id"] = $track_id;
                $track["title"] = $_GET["trtitle"];
                $track["name"] = $_GET["trart"];
                $track["artist_id"] = $_GET["artid"];
                $track["mp3_url"] = $_GET["trurl"];
                
                $_SESSION["playlists"][$plnum]["tracks"][] = $track;
                
                if(isset($_SESSION["user"]))
                {
                    //ajoute la musique a la playlist en base
                }
                break;
            }
        }
        
        //var_dump($_SESSION);
        echo json_encode(true);
    }
    
    function delTrackFromPlaylist()
    {
        $position = $_GET["pos"];
        $playlist_id = $_GET["plid"];
        $temp = array();
        
        //on recherche la playlist concernée par la suppression de musique
        foreach($_SESSION["playlists"] as $plnum => $pl)
        {
            //si c'est la bonne playlist,
            if($pl["playlist_id"] == $playlist_id)
            {
                //on sauvegarde toutes les musiques dans une playlist temporaire, sauf celle concernée par la suppression
                foreach($_SESSION["playlists"][$plnum]["tracks"] as $trpos => $tr)
                {
                    if($trpos != $position)
                    {
                        $temp[] = $tr;
                    }
                }
                
                //on nettoye la playlist en SESSION
                unset($_SESSION["playlists"][$plnum]["tracks"]);
                $_SESSION["playlists"][$plnum]["tracks"] = array();
                
                //et on reconstruit cette playlist en SESSION
                foreach($temp as $trpos => $tr)
                {
                    $_SESSION["playlists"][$plnum]["tracks"][] = $tr;
                }
                
                //si l'user est connecté, on sauvegarde la modif en base
                if(isset($_SESSION["user"]))
                {
                    //suppression de tous les tracks de la playlist en base
                    //insertion de toutes les tracks dans la playlist en base
                }
                
                break;
            }
        }
        
        echo json_encode(true);
    }

    /**
     * Check if the controller has the action
     * @param String $action action to verify
     * @return bool true if the controller has the action
     */
    function hasAction($action)
    {
        return in_array($action, $this->actions);
    }

    /**
     * Check if the current user has the right to go to the called action
     * @param String $action action to verify
     * @return bool true if the user has the right to access to the called action
     * @throws ActionNotFoundException if the action doesn't exist
     */
    function hasRightAccess($action)
    {
        return true;
    }

    /**
     * Call the action to do when the user has no right access
     */
    function accessDenied()
    {
        echo json_encode(array("error" => "403"));
    }


    /**
     * Save playlist to the db
     * Called when the user is connected
     */
    function saveVisitorPlaylistToDatabase(){
        $temp = array();
        foreach($_SESSION["playlists"] as $raw_playlist){
            try {
                $playlist = new Playlist();
                $playlist->setPlaylistName($raw_playlist['playlist_name']);
                $playlist->setUserId(User::getCurrentUser()->getId());
                $playlist->insert();
                $position = 0;
                if (isset($raw_playlist['tracks'])) {
                    PlaylistTrack::insertMultiples($raw_playlist['tracks'], $playlist->getPlaylistId());
                }
                $temp[$playlist->getPlaylistId()] = $raw_playlist;
            }catch (\Exception $e){

            }
        }
        $_SESSION["playlists"] = $temp;
    }
} 