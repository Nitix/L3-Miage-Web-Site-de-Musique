<?php

session_start();

require_once "config.php";

require_once "SplClassLoader.php";

$instance = new SplClassLoader();
$instance->register();
