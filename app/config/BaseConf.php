<?php
global $Ouno_conf;
return $Ouno_conf = array(
    'AUTO_LOAD_PATH'=>array(
        'components'=>'\\components',//自动加载
        'library'=>'\\Ouno\\Core\\Library',
        'db'=>' \\Ouno\\Core\\Db',
    ),
//    'DB'=>array(
//        0=>array(
//        'HOST'=>'127.0.0.1',
//        'DBNAME'=>'crab',
//        'PORT'=>'3306',
//        'USERNAME'=>'root',
//        'PASSWORD'=>'123456',
//        'CHARSET' => 'utf8',
//        'DRIVER'=>'OunoMysql',
//        'PCONNECT'=>false,
//        )
//    ),
    'DB'=>array(
        0=>array(
            'HOST'=>'127.0.0.1',
            'DB'=>'crab',
            'PORT'=>'3306',
            'USERNAME'=>'root',
            'PASSWORD'=>'craber234',
            'CHARSET' => 'utf8',
            'DRIVER'=>'OunoMysqli',
            'AUTO_COMMIT'=>true,
            // 'PCONNECT'=>false,
        )
    ),
    'MONGO'=>array(
        'HOST'=>'127.0.0.1',
        'DBNAME'=>'crab',
        'PORT'=>'27017',
        'USERNAME'=>'crab',
        'PASSWORD'=>'craber234',
        'PCONNECT'=>false,
    ),
    //
    'URI' => 'PATH',
    'LOG_PATH' => '/runtime',
    'EXCEPTION_HANDLE'=>true,
    'ERROR_HANDLE'=>true,
    'ERROR_DISPLAY'=> true,
    'EXCEPTION_DISPLAY'=> true,
    'SERVICE_PATH'=> '/src/service',
    'CONTROLLER_PATH'=>'/web/controller',//控制器

    'TEMPLATE_PATH'=>'/web/template',
    'DAO_PATH'=>'/src/dao',
    'SERVER_PATH'=>'/src/server',
    'RUN_TIME_PATH'=>'/runtime',
    // view type
    'VIEW' => '\\components\\smartyView',
    //smarty config
    'SMARTY_CACHE_DIR'=>'/runtime/smarty/smaryCache',
    'SMARY_COMPILE'=> '/runtime/smarty/compile',
    'SMARTY_PATH' => '/extensions/Smarty/libs',
    'SMARTY_CACHE' => false,

    'VIEW_STATIC_PATH'=> '/runtime/static',

    'DB_DRIVER' => 'OunoMysqli',
    'MODULE'=> true,

    'VIEW_POSTFIX'=> '.html',
    'DEBUG'=> true,
    'BASEURL'=>'http://www.uandc.cn',

    'CONTROLER_NAMESPACE'=>'\\web\\controller',
    'DAO_NAMESPACE'=>'\\src\\dao',
    'SERVICE_NAMESPACE'=>'\\src\\service',
    'COMMAND_NAMESPACE'=>'\\command',
);