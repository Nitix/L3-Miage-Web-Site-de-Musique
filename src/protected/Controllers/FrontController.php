<?php

namespace Controllers;

/**
 * Fetch the correct Controller and call the action, it also verify the access right
 */
class FrontController
{

    /**
     * Default Controller to be called when the controller does not exist
     */
    const DEFAULT_CONTROLLER = 'DefaultController'; //TODO TO be changed

    /**
     * Indicates with controllers exists used to route querys
     * @var array list of controllers (routes)
     */
    private $controllers = array(
        'index' => FrontController::DEFAULT_CONTROLLER,
        'default' => FrontController::DEFAULT_CONTROLLER,
        'base' => 'BaseController',
        'user' => 'UserController',
        'guest' => 'GuestController'
    );


    /**
     * Fetch the correct controller, check if the action exist and call the action, it also verify the access right
     */
    public function init()
    {
        $controller = isset($_GET['c']) ? $_GET['c'] : 'index';
        if (array_key_exists($controller, $this->controllers)) {
            $this->callAction($this->controllers[$controller], $this->getAction());
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
     * @param bool $failSafe Display a fatal error when set at false and when an error occur
     */
    public function callAction($controller, $action = null, array $args = null, $failSafe = true)
    {
        $className = '\Controllers\\' . $controller;
        if (class_exists($className, true)) {
            $instance = new $className();
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
                        $instance->index();
                    } else {
                        $instance->accessDenied();
                    }
                }
            } else {
                $this->displayFatalError("Class " . $controller . " does not implement Controller!");
            }
        } else {
            if ($failSafe) {
                $this->displayError(404, FrontController::DEFAULT_CONTROLLER, 'displayError', array('code' => '404'));
            } else {
                $this->displayFatalError("Class " . $controller . " does not exist!");
            }
        }
    }

    /**
     * Create HTTP error page
     * @param int $errorNumber number error of the http protocol
     * @param String $controller Controller to fallback
     * @param String $action action to call
     * @param array $args arguments to give to the action
     */
    public function displayError($errorNumber, $controller, $action = null, array $args = null)
    {
        switch ($errorNumber) {
            case 404 :
                header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
                $this->callAction($controller, $action, $args, false);
                break;
            default:
                $this->callAction($controller, $action, $args, false);
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
        return isset($_GET['a']) ? $_GET['a'] : ( isset($_POST['a']) ? $_POST['a'] : null );
    }
} 