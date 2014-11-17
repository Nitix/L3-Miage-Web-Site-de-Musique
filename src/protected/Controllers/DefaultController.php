<?php

namespace Controllers;

class DefaultController implements Controller
{

    private static $actions = array(
        'index'
    );

    public function index()
    {
        $view = new \Views\DefaultView();
        $view->display();
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