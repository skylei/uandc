<?php
return $Ouno_conf = array(
//    'AUTO_LOAD_PATH'=>array(
//        'components'=>'\\components',//自动加载
//        'library'=>'\\Ouno\\Core\\Library',
//        'db'=>' \\Ouno\\Core\\Db',
//    ),
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
    'SESSION'=>true,
    'DB'=>array(
        'DEFAULT'=> array(
            0=>array(
                'HOST'=>'192.168.253.9',
                'DB'=>'crab',
                'USERNAME'=>'root',
                'PASSWORD'=>'craber234',
                'CHARSET' => 'utf8',
                'DRIVER'=>'OunoMysqli',
                'AUTO_COMMIT'=>true,
                'CHARSET'=>'utf8'
                // 'PCONNECT'=>false,
            ),
            1=> array(
                'HOST'=>'127.0.0.1',
                'DB'=>'crab',
                'USERNAME'=>'root',
                'PASSWORD'=>'craber234',
                'CHARSET' => 'utf8',
                'DRIVER'=>'OunoMysqli',
                'AUTO_COMMIT'=>true,
                'CHARSET'=>'utf8'
            )
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

    'DB_DRIVER' => 'Ouno\\Db\\OunoMysqli',
    'MODULE'=> true,

    'VIEW_POSTFIX'=> '.html',
    'DEBUG'=> true,
    'BASEURL'=>'http://www.uandc.cn',

    'CONTROLER_NAMESPACE'=>'\\web\\controller',
    'DAO_NAMESPACE'=>'\\src\\dao',
    'SERVICE_NAMESPACE'=>'\\src\\service',
    'COMMAND_NAMESPACE'=>'\\command',
	'SWOOLE'=>array(
		'HOST' => '0.0.0.0', //socket 监听ip
        'PORT' => 8888, //socket 监听端口
        // 'socket_adapter' => 'Swoole', //socket 驱动模块
        // 'client_class' => 'socket\\Server', //socket 回调类
        // swoole server config
        'DEAMONIZE' => 0, //是否开启守护进程
        'WORK_MODE' => 3,
        'WORKER_NUM' => 8,
        'MAX_REQUEST' => 1000,
        'DISPATCH_MODE' => 2,
        'TASK_WORKER_NUM' => 8,
        'OPEN_LENGHT_CHECK' => true,
        'PACKAGE_LENGHT_OFFSET' => 0,
        // 'package_body_offset' => 4,
        // 'package_length_type' => 'N'
	
	
	),
	
);