<?php
error_reporting(-1);
define('APP_PATH', __DIR__ .'/app');
define('CORE_PATH', __DIR__ .'/core');
define('BASE_DIR', __DIR__);
// $config = require(APP_PATH . '/config/BaseConf.php');
require(__DIR__ .DIRECTORY_SEPARATOR . "Ouno/Ouno.php");

\Ouno\Ouno::getInstance()->run(APP_PATH);






