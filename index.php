<?php

ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
//error_reporting(E_ALL);
//error_reporting(E_ALL & ~E_DEPRECATED);


require("./lib/autoload.php");
require_once("./lib/rudrax/boot/RudraX.php");

RudraX::invoke(array(
    'RX_MODE_MAGIC' => TRUE,
    'RX_MODE_DEBUG' => FALSE,
    'PROJECT_ROOT_DIR' => "./"
));
