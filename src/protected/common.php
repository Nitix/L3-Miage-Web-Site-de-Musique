<?php

if (version_compare(phpversion(), '5.3.7', '<')){
    echo "Version 5.3.7 minimum requis (5.5 >= optimal)";
    exit;
}else if (version_compare(phpversion(), '5.5.0', '<')){
    require_once "password.php";
}

require_once "config.php";

require_once "SplClassLoader.php";

$instance = new SplClassLoader();
$instance->register();


session_start();
