<?php


namespace Controllers;

use Models\User;

/**
 * Control the interaction with the user
 * @package Controllers\User
 */
class UserController implements Controller
{

    /**
     * @var array List of actions that can do the client
     */
    private $actions = array(
        'login' => false,
        'check' => true,
        'register' => false,
        'update' => true,
        'view' => false,
        'getPlaylists' => true,
        'addPlaylist' => true
    );

    /**
     * Call the default page of the controller
     */
    function index()
    {
        $currentUser = User::getCurrentUser();
        if ($currentUser->isVisitor()) {
            $this->login();
        } else {
            $this->view();
        }
    }
    
    function getPlaylistsInSession()
    {
        $array = array();
        
        if(isset($_SESSION["playlists"]))
        {
            foreach($_SESSION["playlists"] as $playlist)
            {
                $array[] = array(
                        "playlist_id" => $playlist->getPlaylistId(),
                        "playlist_name" => $playlist->getPlaylistName()
                    );
            }
        }
        
        echo json_encode($array);
    }
    
    function addPlaylistInSession()
    {
        if(!isset($_SESSION["playlists"]))
        {
            $_SESSION["playlists"] = array();
        }
        
        $alreadyExists = false;
        
        foreach($_SESSION["playlists"] as $pl)
        {
            if($pl->getPlaylistName() == $_POST["newPlaylistName"])
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
            $npl = new Playlist();
            $npl->setUserId(null);
            $npl->setPlaylistId(null);
            $npl->setPlaylistName($_POST["newPlaylistName"]);
            
            $_SESSION["playlists"][] = $npl;
            
            $array = array();
            
            foreach($_SESSION["playlists"] as $pl)
            {
                $array[] = array(
                        "playlist_id" => $pl->getPlaylistId(),
                        "user_id" => $pl->getUserId(),
                        "playlist_name" => $pl->getPlaylistName()
                    );
            }
            
            echo json_encode($array);
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
        if (array_key_exists($action, $this->actions)) {
            if ($this->actions[$action]) {
                $currentUser = User::getCurrentUser();
                return !$currentUser->isVisitor();
            } else {
                return true;
            }
        } else {
            throw new ActionNotFoundException();
        }
    }

    /**
     * Call the action to do when the user has no right access
     */
    function accessDenied()
    {
        echo json_encode(array("error" => "403"));
    }

    /**
     * Redirect the user to the login
     */
    public function login()
    {
        echo json_encode(array("url" => array("controller" => "user", "method" => "login")));
    }

    /**
     * Check the login and the password given by POST
     * Connect and register to the session
     */
    public function check()
    {
        if (isset($_POST["login"]) && isset($_POST["password"])) {
            $logged = User::login($_POST["login"], $_POST["password"]);
            if ($logged) {
                echo json_encode(array("success"));
            } else {
                echo json_encode(array(
                    "error" => "2",
                    "url" => array("controller" => "user", "method" => "login"),
                    "message" => "Incorrect password or login"
                ));
            }
        } else {
            echo json_encode(array(
                "error" => "1",
                "url" => array("controller" => "user", "method" => "login"),
                "message" => "Password or login empty"
            ));
        }
    }

    /**
     * Add a user to the database
     * Do all verification before adding :
     * - Valid mail
     * - Not used mail
     * - Password 1 & 2 match
     * - login not used
     * Return an error to client if an verification has not passed
     */
    public function register()
    {
        try {
            $ok = true;
            $errors = array();
            if (!User::isValidEmail($_POST["email"])) {
                array_push($errors, array(10, "Incorrect email"));
                $ok = false;
            } elseif (User::isEmailAlreadyInUse($_POST["email"])) {
                array_push($errors, array(11, "Email already in use"));
                $ok = false;
            }

            if (!User::isValidUsername($_POST["username"])) {
                array_push($errors, array(12, "Incorrect username"));
                $ok = false;
            } elseif (User::isUsernameAlreadyInUse($_POST["username"])) {
                array_push($errors, array(13, "Email already in use"));
                $ok = false;
            }

            if ($_POST['password'] != $_POST['password-check']) {
                array_push($errors, array(14, "Different passwords"));
                $ok = false;
            }

            if (User::isWeakPassword($_POST["password"])) {
                array_push($errors, array(15, "Password too much weak"));
                $ok = false;
            }

            if ($ok) {
                $user = new User();
                $user->setEmail($_POST["email"]);
                $user->setPassword($_POST["password"]);
                $user->setUsername($_POST["username"]);
                $user->insert();
                echo json_encode(array("status" => 0));
            }else {
                echo json_encode(array(
                    "status" => "-1",
                    $errors
                ));
            }
        }catch (\PDOException $e){
            echo json_encode(array("status" => -1));
        }
    }

    /**
     * Update the user
     * Do all verification before updating :
     * - Valid mail
     * - Not used mail
     * - Password 1 & 2 match
     * - login not used
     * Return an error to client if an verification has not passed
     */
    public function update()
    {
        $user = User::getCurrentUser();
        if ($user->isVisitor()) {
            $ok = true;
            $errors = array();
            $changed = false;
            if($user->getEmail() != $_POST["email"]) {
                if (!User::isValidEmail($_POST["email"])) {
                    array_push($errors, array(10, "Incorrect email"));
                    $ok = false;
                } elseif (User::isEmailAlreadyInUse($_POST["email"])) {
                    array_push($errors, array(11, "Email already in use"));
                    $ok = false;
                }
                $changed = true;
            }

            /*
            if (!User::isValidUsername($_POST["username"])) {
                array_push($errors, array(12, "Incorrect username"));
                $ok = false;
            } elseif (User::isUsernameAlreadyInUse($_POST["username"])) {
                array_push($errors, array(13, "Email already in use"));
                $ok = false;
            }
            */
            if(!empty($_POST["password"])) {
                if ($_POST['password'] != $_POST['password-check']) {
                    array_push($errors, array(14, "Different passwords"));
                    $ok = false;
                }

                if (User::isWeakPassword($_POST["password"])) {
                    array_push($errors, array(15, "Password too much weak"));
                    $ok = false;
                }
                $changed = true;
            }

            if ($ok) {
                if($changed) {
                    if ($user->getEmail() != $_POST["email"]) {
                        $user->setEmail($_POST["email"]);
                    }
                    if (!empty($_POST["password"])) {
                        $user->setPassword($_POST['password']);
                    }
                    echo json_encode(array("status" => 0));

                }else{
                    echo json_encode(array("status" => 1));

                }
            }else {
                echo json_encode(array(
                    "status" => "-1",
                    $errors
                ));
            }
        } else {
            echo json_encode(array("status" => "-1"));
        }
    }

    /**
     * Return information about the user to the client
     * Give more information, if it's the current user seeing his profile
     */
    public function view()
    {
        $currentUser = User::getCurrentUser();
        if (isset($_GET['id'])) {
            $user = User::findById($_GET['id']);
            if ($user == null) {
                echo json_encode(array("error" => "404"));
            } else {
                if ($currentUser->isVisitor()) {
                    echo json_encode($this->getInformation($currentUser, false));
                } else {
                    if ($currentUser->getId() == $_POST['id']) {
                        echo json_encode($this->getInformation($user, true));
                    } else {
                        echo json_encode($this->getInformation($user, false));
                    }
                }
            }
        } else {
            if ($currentUser->isVisitor()) {
                echo json_encode($this->getInformation($currentUser, false));
            } else {
                $this->login();
            }
        }
    }

    /**
     * Return information about the user in array
     * @param User $user user where we get information from
     * @param bool $full retrieves all information
     * @return array Information about the users
     */
    private function getInformation(User $user, $full = false)
    {
        //TODO Complete with playlist
        if ($full) {
            return array($user->getUsername(), $user->getEmail());
        } else {
            return array($user->getUsername());
        }
    }
} 