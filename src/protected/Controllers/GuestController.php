<?php


namespace Controllers;

use Models\User;


/**
 * Control the interaction with the user
 * @package Controllers\User
 */
class GuestController implements Controller
{

    /**
     * @var array List of actions that can do the client
     */
    private $actions = array(
        
        'getPlaylists',
        'addPlaylist'
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
       
        if(isset($_SESSION["playlists"]))
        {
            $array = $_SESSION["playlists"];
        }
        
        echo json_encode($array);
    }
    
    function addPlaylist()
    {
        if(!isset($_SESSION["playlists"]))
        {
            $_SESSION["playlists"] = array();
        }
        
        $alreadyExists = false;
        
        foreach($_SESSION["playlists"] as $plnum => $pl)
        {
            
            if($pl["playlist_name"] == $_POST["newPlaylistName"])
            {
                $alreadyExists = true;
                break;
            }
        }
        
        if($alreadyExists)
        {
            echo json_encode(false);
        }
        else
        {
            $npl = array();
            
            if(count($_SESSION["playlists"]) == 0)
            {
                $id = 0;
            }
            else
            {
                $len = count($_SESSION["playlists"]);
                $lastId = $_SESSION["playlists"][$len - 1]["playlist_id"];
                $id = $lastId + 1;
            }
            
            $npl["playlist_id"] = $id;
            $npl["playlist_name"] = $_GET["newPlaylistName"];
            
            $_SESSION["playlists"][] = $npl;
            
            //si l'utilisateur est loggé, on enregistre la modification en base
            if(isset($_SESSION["user"]))
            {
                //ajouter la playlist en base
            }
            
            echo $this->getPlaylists();
        }
        
    }

    /**
     * Check if the controller has the action
     * @param String $action action to verify
     * @return bool true if the controller has the action
     */
    function hasAction($action)
    {
        return array_key_exists($action, $this->actions);
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

 
} 