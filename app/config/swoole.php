<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2015/3/29
 * Time: 14:25
 */
return array(
    'SESSION'=>true,
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
            'CHARSET'=>'utf8'
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
    'RUN_MODE' => array(
        'CLASS' => '\\components\\BaseServer',
        'PARAM' => array(),
    ),
    'SERVER' => array(
        'CLASS'=> '\\components\\BaseSocket',
        'PARAM' => array(),
    ),
    'CLIENT' => array(
        'CLASS'=> '\\components\\BaseClient',
        'PARAM' => array(),
    ),

);