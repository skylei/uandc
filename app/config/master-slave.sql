INSERT INTO crab_comment ( `art_id`, `content`, `email`, `nickname`, `ip`, `user_icon`, `create_time`, `update`) VALUES
( '12','ccccccccccccdddddddddddddddd','craber234@sina.cn','crab','2130706433','/app/static/images/usericon/crab.jpg',
'1428678290','2015-04-10 23:04:50' )
SELECT UNIX_TIMESTAMP()

SHOW SLAVE STATUS;

STOP SLAVE;
CHANGE MASTER TO MASTER_HOST='192.168.253.9',MASTER_USER='backup',
MASTER_PASSWORD='craber234',MASTER_LOG_FILE='mysql-bin.000007' ,MASTER_LOG_POS=107; 
START SLAVE;

STOP SLAVE;
SET GLOBAL sql_slave_skip_counter =1 ;//或删除一的数据
START SLAVE;

Error 'Duplicate entry '1' for key 'PRIMARY'' ON q;
STOP SLAVE;
SET GLOBAL sql_slave_skip_counter =1 ;
START SLAVE;