<?php
error_reporting(-1);
define('APP_PATH', __DIR__ .'/app');
define('CORE_PATH', __DIR__ .'/core');
require("./Ouno/Ouno.php");

\Ouno\Ouno::getInstance()->run(APP_PATH, 'BaseConf');
//require(APP_PATH . '/config/BaseConf.php');
//$app = \Ouno\Ouno::getInstance();


