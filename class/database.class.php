<?php
require_once(__DIR__.'/../cfg/database.cfg.php');
require_once(__DIR__.'/../cfg/site.cfg.php');
require_once(__DIR__.'/../functions/common.func.php');
class database extends mysqli {
	function __construct(){
		global $cfg;
		$this->mysqli($cfg['db_host'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_dtbs']);
		$cfg['debug'] && $this->errno && dbgp('<pre>DEBUG:DATABASE ERROR('.$this->errno.'):'.$this->error.'</pre>');
		$this->query('SET NAMES UTF8');
	}
	/**
	* 插入拍品
	* @param string $name 拍品名称
	* @param string $des 拍品描述
	* @param string $donor 捐赠人信息
	* @param int $status 拍品状态
	* @param string $imgUrl 图片url,用逗号(,)分隔
	* @return mixed
	*/
	function insertGood($name, $category, $des, $donor, $status, $imgUrl){
		$stmt = $this->prepare('INSERT INTO `goods_info`(`name`,`category`,`description`,`donorinfo`,`status`,`imgurl`)VALUES(?, ?, ?, ?, ?, ?)');
		$stmt->bind_param('ssssis',$a, $b, $c, $d, $e, $f);
		$a = $name; $b = $category; $c = $des; $d = $donor; $e = $status; $f = $imgUrl;
		$stmt->execute();
		$r = $stmt->insert_id;
		$stmt->close();
		$stmt = $this->prepare('SELECT `cateid` FROM `goods_categories_info` WHERE `name` = ?');
		$stmt->bind_param('s',$a);
		$a = $category;
		$stmt->execute();
		$stmt->bind_result($id);
		if(!$stmt->fetch()){
			$stmt->close();
			$stmt = $this->prepare('INSERT INTO `goods_categories_info`(`name`)VALUES(?)');
			$stmt->bind_param('s',$a);
			$a = $category;
			$stmt->execute();
		}
		$stmt->close();
		return $r > 0 ? $r : false;
	}

	/**
	* 删除拍品
	* @param int $id 拍品id
	* @return mixed
	*/
	function deleteGood($id){
		$stmt = $this->prepare('DELETE FROM `goods_info` WHERE `id` = ?');
		$stmt->bind_param('i',$a);
		$a = $id;
		$r = $stmt->execute();
		$stmt->close();
		return $r;
	}

	/**
	* 删除拍品
	* @param string $name 拍品名字
	* @return mixed
	*/
	function deleteGoodByName($name){
		$name = $this->real_escape_string($name);
		$k = $this->query('SELECT count(*) FROM `goods_info` WHERE `category` = (SELECT `category` FROM `goods_info` WHERE `name` = \''.$name.'\' LIMIT 1)');
		if($row = $k->fetch_array()){
			if($row[0] == 1){
				$this->query('DELETE FROM `goods_categories_info` WHERE `name` = (SELECT `category` FROM `goods_info` WHERE `name` = \''.$name.'\' LIMIT 1)');
			}
		}
		$stmt = $this->prepare('DELETE FROM `goods_info` WHERE `name` = ?');
		$stmt->bind_param('s',$a);
		$a = $name;
		$r = $stmt->execute();
		$stmt->close();
		return $r;
	}

	/**
	* 分页查找拍品
	* @param int $start 拍品开始位置
	* @param int $length 拍品长度
	* @param int $status 拍品状态
	* @param boolean $desc 是否倒序查询
	* @return array
	*/
	function selectGoods($start=0, $length=9, $status=101, $desc=false){
	/*	$stmt = $this->prepare('SELECT * FROM `goods_info` WHERE `status` = ? ORDER BY `id` '.($desc?'DESC':'ASC') .' LIMIT ?, ? ');
		$stmt->bind_param('iii',$a,$b,$c);
		$a = $status; $b = $start; $c = $length;
		$stmt->execute();
		$stmt->bind_result($id,$name,$category,$description,$donorInfo,$status,$transactionAmount,$imgUrl);
		$r = array();
		while($stmt->fetch()){
			$r[] = array(
				'id'                => $id,
				'name'              => $name,
				'category'          => $category,
				'description'       => $description,
				'donorInfo'         => $donorInfo,
				'status'            => $status,
				'transactionAmount' => $transactionAmount,
				'imgUrl'            => $imgUrl,
			);
		}
		$stmt->close();*/
		$status = $this->real_escape_string($status);
		$start = $this->real_escape_string($start);
		$length = $this->real_escape_string($length);
		$res = $this->query('SELECT * FROM `goods_info` WHERE `status` = '.$status.' ORDER BY `id` '.($desc?'DESC':'ASC') .' LIMIT '.$start.', '.$length.' ');
		while($row = $res->fetch_array()){
			$r[] = array(
				'id'                => $row[0],
				'name'              => $row[1],
				'category'          => $row[2],
				'description'       => $row[3],
				'donorInfo'         => $row[4],
				'status'            => $row[5],
				'transactionAmount' => $row[6],
				'imgUrl'            => $row[7],
			);
		}
		return $r;
	}

	function selectCategories(){
		$stmt = $this->prepare('SELECT * FROM `goods_categories_info`');
		$stmt->execute();
		$stmt->bind_result($id,$name);
		while($stmt->fetch()){
			$re[] = array(
				'id' => $id,
				'name' => $name,
			);
		}
		$stmt->close();
		return $re;
	}

	function selectGoodsByCategoryId($cid,$start=0,$length=9){
		$cid = $this->real_escape_string($cid);
		$start = intval($start);
		$length =intval($length);
		$r = $this->query('SELECT * FROM `goods_info` WHERE `category` = (SELECT `name` FROM `goods_categories_info` WHERE `cateid` = '.$cid.') LIMIT '.$start.','.$length.'');
		while($row = $r->fetch_assoc()){
			$res[] = $row;
		}
		return $res;
	}

	function selectCateImgUrl($cateName){
		$stmt = $this->prepare('SELECT `imgurl` FROM `goods_info` WHERE `category` = ? LIMIT 1');
		$stmt->bind_param('s',$a);
		$a = $cateName;
		$stmt->execute();
		$stmt->bind_result($imgUrl);
		if($stmt->fetch()){
			$stmt->close();
			$t = explode(',',$imgUrl);
			return $t[0];
		}else{
			$stmt->close();
			return '';
		}
	}

	function selectComments($gid){
		$gid = $this->real_escape_string($gid);
		$r = $this->query('SELECT * FROM `goods_comments` WHERE `gid` = '.$gid);
		while($row = $r->fetch_assoc()){
			$res[] = $row;
		}
		return $res;
	}

	/**
	* 通过id查找拍品
	* @param int $id 拍品id
	* @return mixed
	*/
	function selectGoodById($id){
		$stmt = $this->prepare('SELECT * FROM `goods_info` WHERE `id` = ? LIMIT 1');
		$stmt->bind_param('i',$a);
		$a = $id;
		$stmt->execute();
		$stmt->bind_result($id,$name,$category,$description,$donorInfo,$status,$transactionAmount,$imgUrl);
		if($stmt->fetch()){
			$stmt->close();
			return array(
				'id'                => $id,
				'name'              => $name,
				'category'          => $category,
				'description'       => $description,
				'donorInfo'         => $donorInfo,
				'status'            => $status,
				'transactionAmount' => $transactionAmount,
				'imgUrl'            => $imgUrl,
			);
		}else{
			$stmt->close();
			return false;
		}
	}

	/**
	* 修改拍品信息
	* @param int $id 拍品id
	* @param string $name 拍品名称
	* @param string $des 拍品描述
	* @param string $donor 捐赠人信息
	* @param int $status 拍品状态
	* @param float
	* @param string $imgUrl 图片url,用逗号(,)分隔
	* @return boolean
	*/
	function updateGood($id ,$name, $category, $des, $donor, $status, $tra, $imgUrl){
		$stmt = $this->prepare('UPDATE `goods_info` SET `name` = ? , `category` = ? , `description` = ? , `donorinfo` = ? , `status` = ? , `transactionamount` = ? , `imgurl` = ? WHERE `id` = ?');
		$stmt->bind_param('ssssidsi',$a,$b,$c,$d,$e,$f,$g,$h);
		$a = $name; $b = $category; $c = $des; $d = $donor; $e = $status; $f = $tra; $g = $imgUrl; $h = $id;
		$r = $stmt->execute();
		$stmt->close();
		return $r > 0 ? true : false;
	}

	/**
	* 获取access_token
	* @return string
	*/
	function getAccessToken(){
		$r = $this->query('SELECT * FROM `wx_relevant_info` WHERE `key` = \'access_token\' LIMIT 1');
		$row = $r->fetch_assoc();
		return $row['value'];
	}

	/**
	* 测试access_token是否过期
	* @return boolean
	*/
	function testAccessToken($tk){
		//通过请求微信服务器ip来判断access_token是否过期
		$g = file_get_contents('https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$tk);
		$g = json_decode($g, true);
		if(isset($g['errcode']) && $g['errcode']) return false;
		return true;
	}

	/**
	* 更新access_token
	* @return string
	*/
	function updateAccessToken(){
		global $cfg;
		$r = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$cfg['wx_appID'].'&secret='.$cfg['wx_appsecret']);
		$r = json_decode($r, true);
		$stmt = $this->prepare('UPDATE `wx_relevant_info` set `value` = ? WHERE `key` = \'access_token\'');
		$stmt->bind_param('s',$a);
		$a = $r['access_token'];
		$stmt->execute();
		$stmt->close();
		return $r['access_token'];
	}

	/**
	* 获取jsapi_ticket
	* @return string
	*/
	function getJsApiTicket(){
		$r = $this->query('SELECT * FROM `wx_relevant_info` WHERE `key` = \'jsapi_ticket\' LIMIT 1');
		$row = $r->fetch_assoc();
		return $row['value'];
	}

	/**
	* 更新jsapi_ticket
	* @return string
	*/
	function updateJsApiTicket(){
		$accessToken = $this->getAccessToken();
		$r = file_get_contents('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$accessToken.'&type=jsapi');
		$r = json_decode($r, true);
		if($r['errcode'] !== 0){
			$accessToken = $this->updateAccessToken();
		}else{
			$accessToken = $this->getAccessToken();
			$r = file_get_contents('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$accessToken.'&type=jsapi');
			$r = json_decode($r, true);
		}
		$stmt = $this->prepare('UPDATE `wx_relevant_info` SET `value` = ? WHERE `key` = \'jsapi_ticket\'');
		$stmt->bind_param('s',$a);
		$a = $r['ticket'];
		$stmt->execute();
		$stmt->close();
		return $a;
	}

	/**
	* 检查用户名密码
	* @param $name 管理员用户名
	* @param $psss 管理员密码
	* @return mixed
	*/
	function checkUserPass($name,$pass){
		global $cfg;
		$stmt = $this->prepare('SELECT `uid` FROM `admin_info` WHERE `name` = ? AND `password` = ? LIMIT 1');
		$stmt->bind_param('ss',$a,$b);
		$a = $name; $b = md5($pass.$cfg['passSalt']);
		$stmt->execute();
		$stmt->bind_result($uid);
		$stmt->fetch();
		$stmt->close();
		return $uid > 0 ? $uid : false;
	}

	/**
	* 修改用户名密码,这个会一次性把用户名和密码都设置,谨慎使用!!
	* @param $uid 用户id
	* @param $name 管理员用户名
	* @param $pass 管理员密码
	* @return boolean
	*/
	function updateUserPass($uid, $name, $pass){
		global $cfg;
		$stmt = $this->prepare('UPDATE `admin_info` SET `name` = ? , `password` = ? WHERE `uid` = ?');
		$stmt->bind_param('ssi',$a,$b,$c);
		$a = $name; $b = md5($pass.$cfg['passSalt']); $c = $uid;
		$r = $stmt->execute();
		$stmt->close();
		return $r;
	}
}
?>