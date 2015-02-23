<?php
// error_reporting( E_ERROR |  E_PARSE);
define('APP_PATH', __DIR__ .'/app');
define('CORE_PATH', __DIR__ .'/core');
define('BASE_DIR', __DIR__);
$config = require(APP_PATH . '/config/BaseConf.php');
require('/Ouno/Ouno.php');

\Ouno\Ouno::getInstance($config);
//require(APP_PATH . '/config/BaseConf.php');
//$app = \Ouno\Ouno::getInstance();


