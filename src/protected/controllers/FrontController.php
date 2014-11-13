<?php

include "Controller.php";

/**
 * Class FrontController
 *
 * Front Controller, fetch the correct Controller and call the action, it also verify the access right
 */
class FrontController
{

    /**
     * Default Controller to be called when the controller does not exist
     */
    const DEFAULT_CONTROLLER = 'IndexController'; //TODO TO be changed

    /**
     * Indicates with controllers exists used to route querys
     * @var array list of Controllers (routes)
     */
    private $controllers = array(
        'index' => FrontController::DEFAULT_CONTROLLER,
        'default' => FrontController::DEFAULT_CONTROLLER

        //'membres' => 'UserController'
    );


    /**
     * Fetch the correct controller, check if the action exist and call the action, it also verify the access right
     */
    public function init()
    {
        if (array_key_exists($_GET['c'], $this->controllers)) {
            $this->callAction($_GET['c'], $this->getAction());
        } else {
            $this->callAction(FrontController::DEFAULT_CONTROLLER);
        }
    }

    /**
     * Call the action of the specified controller
     * An empty or null action will call the default action of the controller
     * @param String $controller the controller to call
     * @param String $action the action to call
     * @param array $args arguments to give to the action
     */
    public function callAction($controller, $action = null, array $args = null)
    {
        if (class_exists($controller, true)) {
            $instance = new $controller();
            if ($instance instanceof Controller) {  //Controller must implements interface Controller
                if ($action != null && !empty($action)) { //Check if action exist
                    if ($instance->hasAction($action)) {
                        if ($instance->hasRightAccess($action)) {
                            $instance->$action($args);
                        } else {
                            $instance->accessDenied();
                        }
                    } else {
                        if ($instance->hasRightAccess("index")) {
                            $instance->$action($instance->index());
                        } else {
                            $instance->accessDenied();
                        }
                    }
                } else {
                    if ($instance->hasRightAccess("index")) {
                        $instance->$action($instance->index());
                    } else {
                        $instance->accessDenied();
                    }
                }
            } else {
                $this->displayFatalError("Class " . $controller . " does not implement Controller !");
            }
        } else {
            $this->displayError(404, FrontController::DEFAULT_CONTROLLER, 'displayError', array('code' => '404'));
        }
    }

    /**
     * Create HTTP error page
     * @param int $errorNumber http
     * @param String $controller Controller to fallback
     * @param String $action action to call
     * @param array $args arguments to give to the action
     */
    public function displayError($errorNumber, $controller, $action = null, array $args = null)
    {
        switch ($errorNumber) {
            case 404 :
                header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
                $this->callAction($controller, $action, $args);
                break;
            default:
                $this->callAction($controller, $action, $args);
                break;
        }
    }

    /**
     * To be used only because of wrong programming
     * @param String $message message to display
     */
    public function displayFatalError($message)
    {
        echo "Une erreur fatale a survenu : $message";
    }

    /**
     * Return the action called by the user (POST or GET)
     * @return String|null return the action
     */
    private function getAction()
    {
        return isset($_GET['a']) ? $_GET['a'] : isset($_POST['a']) ? $_POST['a'] : null;
    }
} 