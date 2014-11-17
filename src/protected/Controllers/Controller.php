<?php

namespace Controllers;

/**
 * This interface define all the basics action that must implements Controllers
 */
interface Controller
{

    /**
     * Call the default page of the controller
     */
    function index();

    /**
     * Check if the controller has the action
     * @param String $action action to verify
     * @return bool true if the controller has the action
     */
    function hasAction($action);

    /**
     * Check if the current user has the right to go to the called action
     * @param String $action action to verify
     * @return bool true if the user has the right to access to the called action
     * @throws ActionNotFoundException if the action doesn't exist
     */
    function hasRightAccess($action);

    /**
     * Call the action to do when the user has no right access
     */
    function accessDenied();
} 