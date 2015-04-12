<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/4/11
 * Time: 19:31
 */
error_reporting(-1);
define('APP_PATH', __DIR__ .'/app');
define('CORE_PATH', __DIR__ .'/core');
require("./Ouno/Ouno.php");
\Ouno\Ouno::getInstance()->run(APP_PATH, 'rest');