<?php
/* 此文件为更新微信需要我们服务器定时更新用的一个脚本，更新频率一天不得超过2k次 */
require_once(__DIR__.'/../class/database.class.php');
$db = new database();
$cfg['debug'] && dbgp('<pre>access_token:'.$db->updateAccessToken()."\n");
$cfg['debug'] && dbgp('jsapi_ticket:'.$db->updateJsApiTicket().'</pre>');
echo 'ok';
?>