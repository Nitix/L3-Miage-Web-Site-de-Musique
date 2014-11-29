<?php

namespace Controllers;

class ConnexionGiver
{
    private static $host = "";
    private static $login = "";
    private static $password = "";
    private static $database = "";
    
    private static $connexion = null;
    
    public static function getConnexion()
    {
        if(!isset(ConnexionGiver::$connexion))
        {
            ConnexionGiver::$connexion = mysql_connect(ConnexionGiver::$host,ConnexionGiver::$login,ConnexionGiver::$password);
        }
        
        return ConnexionGiver::$connexion;
        
    }
    
    public static function getDatabase()
    {
        return mysql_select_db(ConnexionGiver::$database, ConnexionGiver::getConnexion());
    }
} 