<?php

session_start();

require_once "SplClassLoader.php";

$instance = new SplClassLoader();
$instance->register();
