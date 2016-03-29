<?php
header("Access-Control-Allow-Origin:*");
require_once(__DIR__.'/class/database.class.php');
require_once(__DIR__.'/functions/common.func.php');
if($_GET['f'] === 'getAllItem'){
	session_start();
	if(!$_SESSION['admin']){
		die('无权限');
	}
	$db = new database();
	$r = $db->selectGoods(0,99999999);
	foreach ($r as $k => $v) {
		$out['t_'.$k]['name'] = htmlspecialchars_decode($v['name']);
		$tArr = explode(',', $v['imgUrl']);
		$out['t_'.$k]['img'] = $tArr[0];
		$out['t_'.$k]['cate'] = htmlspecialchars_decode($v['category']);
		$out['t_'.$k]['donor'] = htmlspecialchars_decode($v['donorInfo']);
		$v['auction_info'] = json_decode($v['auction_info'],true);
		$out['t_'.$k]['price'] = $v['auction_info'][0]['price'];
		// var_dump($v['auction_info']);
		unset($v['auction_info'][0]);
		$out['t_'.$k]['auctionInfos'] = $v['auction_info'];
		$out['t_'.$k]['summary'] = htmlspecialchars_decode($v['description']);
	}
	echo json_encode($out);
}else if($_GET['f'] === 'deleteItem'){
	session_start();
	if(!$_SESSION['admin']){
		die('无权限');
	}
	$db = new database();
	if($db->deleteGoodByName($_GET['name']))
		echo 'true';
}else if($_GET['f'] === 'getAuctionItems'){
	$db = new database();
	$cateId = intval($_GET['categoryId']);
	$r = $db->query('SELECT count(*) FROM `goods_info` WHERE `category` = (SELECT `name` FROM `goods_categories_info` WHERE `cateid` = '.$cateId.')');
	$t = $r->fetch_array();
	$count = $t[0];
	$out['totalPages'] = ceil($count/12);
	$out['currentPage'] = intval($_GET['currentPage']);
	$r = $db->selectGoodsByCategoryId($_GET['categoryId'],($out['currentPage']-1)*12,12);
	$out['category']['name'] = $r[0]['category'];
	$j = 0;
	foreach ($r as $k => $v) {
		$out['category']['auctions'][$j]['id'] = htmlspecialchars_decode($v['id']);
		$t = explode(',',$v['imgurl']);
		$i = 1;
		foreach ($t as $kk => $vv) {
			if($vv == '') continue;
			$img['path'.$i++] = $vv;
		}
		$out['category']['auctions'][$j]['img'] = $img;
		$out['category']['auctions'][$j++]['name'] = htmlspecialchars_decode($v['name']);
	}
	echo json_encode($out);
}else if($_GET['f'] === 'getCategories'){
	$db = new database();
	$r = $db->selectCategories();
	foreach ($r as $k => $v) {
		$out[$k]['id'] = $v['id'];
		$out[$k]['name'] = $v['name'];
		$out[$k]['img'] = $db->selectCateImgUrl($v['name']);
	}
	echo json_encode($out);
}else if($_GET['f'] === 'getDetail'){
	$gid = $_GET['id'];
	$db = new database();
	$auctionMeta = $db->selectGoodById($gid);
	$commentsMeta = $db->selectComments($gid);
	$acif = json_decode($auctionMeta['auction_info'],true);
	$out['auctionItem'] = array(
		'name'      => htmlspecialchars_decode($auctionMeta['name']),
		'summary'   => htmlspecialchars_decode($auctionMeta['description']),
		'donor'     => htmlspecialchars_decode($auctionMeta['donorInfo']),
		'price'     => $acif[0]['price'],
		'likeCount' => $auctionMeta['like_count'],
	);
	usort($acif, 'cmp');
	$out['auctionItem']['topPrice'] = $acif[0]['price'];
	$imgArr = explode(',', $auctionMeta['imgUrl']);
	$i = 0;
	foreach ($imgArr as $v) {
		if($v == '') continue;
		$out['auctionItem']['imgs'][$i++] = str_replace('thumb/', '', $v);
	}
	$i = 0;
	if(is_array($commentsMeta)){
		foreach ($commentsMeta as $v) {
			$out['comments'][$i]['user']['name'] = htmlspecialchars_decode($v['name']);
			$out['comments'][$i]['user']['img'] = htmlspecialchars_decode($v['img']);
			$out['comments'][$i++]['content'] = htmlspecialchars_decode($v['content']);
		}
	}
	$myInfo = json_decode(getUserInfo($_COOKIE['auction_ssid']),true);
	$out['myInfo'] = $myInfo;
	echo json_encode($out);
}else if($_GET['f'] === 'clickLike'){
	$db = new database();
	$db->clickLike($_GET['gid']);
}

function cmp($a,$b){
	if($a['price'] == $b['price']){
		return 0;
	}
	return $a['price'] < $b['price'] ? 1 : -1;
}