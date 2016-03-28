<?php
error_reporting(E_ALL);
require_once(__DIR__.'/functions/common.func.php');
if(isset($_GET['code'])){
	$userTokenMeta = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$cfg['wx_appID'].'&secret='.$cfg['wx_appsecret'].'&code='.$_GET['code'].'&grant_type=authorization_code');
	$r = json_decode($userTokenMeta,true);
	if(isset($r['errcode']) && $r['errcode']){
		die('用户取消授权');
	}
	$userInfoMeta = file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$r['access_token'].'&openid='.$r['openid'].'&lang=zh_CN');
	$ssid = randomSalt();
	setcookie('auction_ssid',$ssid,time()+60*60*24*365,'/');
	$_COOKIE['auction_ssid'] = $ssid;
	$db = new database();
	$db->query('INSERT INTO `wx_user_info`(`user_info_meta`,`user_token_meta`,`session_id`) VALUES(\''.$userInfoMeta.'\',\''.$userTokenMeta.'\',\''.$ssid.'\')');
}
$isLogin = false;
if(isset($_COOKIE['auction_ssid']) && $_COOKIE['auction_ssid']){
	$db = new database();
	$ssid = $db->real_escape_string($_COOKIE['auction_ssid']);
	$r = $db->query('SELECT * FROM `wx_user_info` WHERE `session_id` = \''.$ssid.'\' LIMIT 1');
	if($r && $row = $r->fetch_assoc()){
		$tkm = json_decode($row['user_token_meta'],true);
		//检查用户access_token和openid是否还有效
		if(checkUserToken($tkm['access_token'], $tkm['openid'])){
			$isLogin = true;
		}else{
			//检查用户refresh_token是否有效且更新
			if(refreshUserToken($_COOKIE['auction_ssid'])){
				$isLogin = true;
				updateUserInfo($_COOKIE['auction_ssid']);
			}
		}
	}
}

if(!isset($_GET['code']) && !$isLogin){
	$url_self = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	header('Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$cfg['wx_appID'].'&redirect_uri='.urlencode($url_self).'&response_type=code&scope=snsapi_userinfo#wechat_redirect');
	die();
}
echo "登录成功！";
if($isLogin && isset($_GET['linkTo'])){
	header('Location: '.urldecode($_GET['linkTo']));
	die();
}

?>