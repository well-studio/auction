<?php
date_default_timezone_set('Asia/Shanghai');
require_once(__DIR__.'/../class/database.class.php');
require_once(__DIR__.'/../cfg/site.cfg.php');
/* debug print */
function dbgp($str){
	echo $str;
	return 1;
}

function getDonorInfo($length=5){
	if(!file_exists(__DIR__.'/../log/donor_info_'.date('Y').'.csv')){
		return array(
			'errno'  => '40001',
			'errmsg' => '今年还没有人捐款哦',
		);
	}else{
		$content = file_get_contents(__DIR__.'/../log/donor_info_'.date('Y').'.csv');
		$r = explode("\n", $content);
		unset($r[count($r) - 1]);unset($r[0]);
		$r = array_reverse($r);
		$len = count($r);
		$ret = array();
		for($i=0;$i<$len && $i<$length;$i++){
			$k = explode(',', $r[$i]);
			$ret[] = array(
				'wx_nickName' => $k[0],
				'donor_money' => $k[1],
				'donor_time'  => $k[2],
			);
		}
		return $ret;
	}
}

function insertDonorInfo($wx_name,$money,$time,$realName='',$info=''){
	if(!file_exists(__DIR__.'/../log/donor_info_'.date('Y').'.csv')){
		file_put_contents(__DIR__.'/../log/donor_info_'.date('Y').'.csv', "wx_name,donor_money,donor_time,donor_db_uid\n");
	}
	if($realName !== ''){
		$db   = new database();
		$stmt = $db->prepare('INSERT INTO `donor_info`(`name`,`info`)VALUES(?, ?)');
		$stmt->bind_param('ss', $a, $b);
		$a    = $realName; $b = $info;
		$stmt->execute();
		$did  = $stmt->insert_id;
		$stmt->close();
	}else{
		$did = 0;
	}
	$wx_name = str_replace(',', '，', $wx_name);
	$money   = str_replace(',', '，', $money);
	$time    = str_replace(',', '，', $time);
	file_put_contents(__DIR__.'/../log/donor_info_'.date('Y').'.csv',"{$wx_name},{$money},{$time},{$did}\n",FILE_APPEND);
}

function getMaxPriceInfo($gid){
	$db = new database();
	$r = $db->selectAuctionPrice($gid);
	if(!is_array($r)) return false;
	$max = $r[0];
	for($cnt=count($r),$i=1;$i<$cnt;$i++){
		if($max['price']<$r['price']) $max = $r;
	}
	return $max;
}


//随机盐
function randomSalt(){
	$t = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$r = '';
	for($i=0;$i<32;$i++){
		$r .= $t{rand(0,62)};
	}
	return $r;
}


/* 微信api相关 */
function getWxJsConfig(){
	global $cfg;
	$db = new database();
	$timestamp = time();
	$nonceStr = randomSalt();
	$ticket = $db->getJsApiTicket();
	$ret = array(
		'appID'     => $cfg['wx_appID'],
		'timestamp' => $timestamp,
		'nonceStr'  => $nonceStr,
		'signature' => getWxJsSignature($ticket, $timestamp, $nonceStr),
	);
	return $ret;
}
function checkUserToken($actk, $opid){
	$r = file_get_contents('https://api.weixin.qq.com/sns/auth?access_token='.$actk.'&openid='.$opid);
	$r = json_decode($r,true);
	return $r['errcode'] == 0 ? true : false;
}
function refreshUserToken($ssid){
	global $cfg;
	$db = new database();
	$ssid = $db->real_escape_string($ssid);
	$r = $db->query('SELECT * FROM `wx_user_info` WHERE `session_id` = \''.$ssid.'\' LIMIT 1');
	if($row = $r->fetch_assoc()){
		$tkm = json_decode($row['user_token_meta'],true);
		$r = json_decode(file_get_contents('https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$cfg['wx_appID'].'&grant_type=refresh_token&refresh_token='.$tkm['refresh_token']),true);
		if(isset($r['errcode']) && $r['errcode']) return false;
		$r = $db->real_escape_string(json_encode($r));
		$db->query('UPDATE `wx_user_info` SET `user_token_meta` = \''.$r.'\' WHERE `session_id` = \''.$ssid.'\'');
		echo $db->error;
		return true;
	}else{
		return false;
	}
}
function updateUserInfo($ssid){
	$db = new database();
	$ssid = $db->real_escape_string($ssid);
	$r = $db->query('SELECT * FROM `wx_user_info` WHERE `session_id` = \''.$ssid.'\' LIMIT 1');
	if($row = $r->fetch_assoc()){
		$tkm = json_decode($row['user_token_meta'],true);
		$r = json_decode(file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$tkm['access_token'].'&openid='.$tkm['openid'].'&lang=zh_CN'),true);
		if(isset($r['errcode']) && $r['errcode']) return false;
		$r = $db->real_escape_string(json_encode($r));
		$db->query('UPDATE `wx_user_info` SET `user_info_meta` = \''.$r.'\' WHERE `session_id` = '.$ssid.'\'');
		return $r;
	}else{
		return false;
	}
}
function wxUserPage(){
	global $cfg;
	$url_self = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$isLogin = false;
	if(isset($_COOKIE['auction_ssid'])){
		if(getUserInfo($_COOKIE['auction_ssid'])){
			return;
		}
	}
	header('Location: '.$cfg['siteUrl'].'/login_wx.php?linkTo='.$url_self);
	die();
}
function getUserInfo($ssid){
	$db = new database();
	$ssid = $db->real_escape_string($ssid);
	$r = $db->query('SELECT * FROM `wx_user_info` WHERE `session_id` = \''.$ssid.'\'');
	if($ssid && $r && $row = $r->fetch_assoc()){
		$userInfo = json_decode($row['user_info_meta'],true);
		$userToken = json_decode($row['user_token_meta'],true);
		if(checkUserToken($userToken['access_token'],$userToken['openid'])){
			$t = json_decode($row['user_info_meta'],true);
			$t['uid'] = $row['uid'];
			return json_encode($t);
		}else{
			return updateUserInfo($ssid);
		}
	}else{
		return false;
	}
}


/*
签名生成规则如下：参与签名的字段包括noncestr（随机字符串）, 有效的jsapi_ticket, timestamp（时间戳）, url（当前网页的URL，不包含#及其后面部分） 。对所有待签名参数按照字段名的ASCII 码从小到大排序（字典序）后，使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串string1。这里需要注意的是所有参数名均为小写字符。对string1作sha1加密，字段名和字段值都采用原始值，不进行URL 转义。
*/
function getWxJsSignature($ticket, $timestamp, $nonce){
	$ticket = 'jsapi_ticket='.$ticket;
	$timestamp = 'timestamp='.$timestamp;
	$nonce = 'noncestr='.$nonce;
	$url = 'url=http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];;
	$arr = array($ticket, $timestamp, $nonce, $url);
	sort($arr, SORT_STRING);
	return sha1(implode('&',$arr));
}
?>
