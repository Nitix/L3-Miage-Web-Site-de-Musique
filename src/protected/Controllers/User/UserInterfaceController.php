<?php


namespace Controllers\User;


use Controllers\ActionNotFoundException;
use Controllers\Controller;

/**
 * Control the interaction with the user
 * @package Controllers\User
 */
class UserInterfaceController implements Controller
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
    );

    /**
     * Call the default page of the controller
     */
    function index()
    {
        $currentUser = UserController::getCurrentUser();
        if ($currentUser->isVisitor()) {
            $this->login();
        } else {
            $this->view();
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
                $currentUser = UserController::getCurrentUser();
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
            $logged = UserController::login($_POST["login"], $_POST["password"]);
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
        $ok = true;
        $errors = array();
        $checkEmail = UserController::checkEmail($_POST["email"]);
        if ($checkEmail == UserController::INVALID_EMAIL) {
            array_push($errors, array(UserController::INVALID_EMAIL, "Incorrect email"));
            $ok = false;
        } elseif ($checkEmail == UserController::EMAIL_ALREADY_IN_USE) {
            array_push($errors, array(UserController::EMAIL_ALREADY_IN_USE, "Email already in use"));
            $ok = false;
        }

        if (!UserController::checkLogin($_POST["login"])) {
            array_push($errors, array(12, "Incorrect email"));
            $ok = false;
        }

        if ($_POST['password'] != $_POST['password-check']) {
            array_push($errors, array(13, "Different passwords"));
            $ok = false;
        }

        if (!UserController::checkPasswordStrength($_POST["password"])) {
            array_push($errors, array(14, "Password too much weak"));
            $ok = false;
        }

        if ($ok) {
            $res = UserController::register($_POST["login"], $_POST['email'], $_POST['password']);
            if ($res) {
                echo json_encode("success");
            } else {
                echo json_encode("error");
            }
        }
        echo json_encode(array(
            "error" => "1",
            $errors
        ));
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
        $ok = true;
        $errors = array();
        $user = UserController::getCurrentUser();
        if ($user->isVisitor()) {
            $checkEmail = UserController::checkEmail($_POST["email"]);
            if ($checkEmail == UserController::INVALID_EMAIL) {
                array_push($errors, array(UserController::INVALID_EMAIL, "Incorrect email"));
                $ok = false;
            }

            if (!$user->checkOtherEmail($_POST["email"])) {
                array_push($errors, array(UserController::EMAIL_ALREADY_IN_USE, "Email already in use"));
                $ok = false;
            }

            if ($_POST['password'] != $_POST['password-check']) {
                array_push($errors, array(13, "Different passwords"));
                $ok = false;
            }

            if (!UserController::checkPasswordStrength($_POST["password"])) {
                array_push($errors, array(14, "Password too much weak"));
                $ok = false;
            }

            if ($ok) {

                $res = $user->update($_POST['email'], $_POST['password']);
                if ($res) {
                    echo json_encode("success");
                } else {
                    echo json_encode("error");
                }

            }
            echo json_encode(array(
                "error" => "1",
                $errors
            ));
        } else {
            echo json_encode("error");
        }
    }

    /**
     * Return information about the user to the client
     * Give more information, if it's the current user seeing his profile
     */
    public function view()
    {
        $currentUser = UserController::getCurrentUser();
        if (isset($_GET['id'])) {
            $user = User::findById($_GET['id']);
            if ($user == null) {
                echo json_encode(array("error" => "404"));
            } else {
                if ($currentUser->getUser()->getId() == $_POST['id']) {
                    echo json_encode($this->getInformation($user, true));
                } else {
                    echo json_encode($this->getInformation($user, false));
                }
            }
        } else {
            if ($currentUser->isVisitor()) {
                echo json_encode($this->getInformation($currentUser->getUser(), false));
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
            return array($user->getPseudo(), $user->getEmail());
        } else {
            return array($user->getPseudo());
        }
    }
} 