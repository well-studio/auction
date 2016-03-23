<?php
header("Access-Control-Allow-Origin:*");
require_once(__DIR__.'/class/database.class.php');
if($_GET['f'] === 'getAllItem'){
	$db = new database();
	$r = $db->selectGoods(0,99999999);
	// var_dump($r);
	foreach ($r as $k => $v) {
		$out['t_'.$k]['name'] = $v['name'];
		$tArr = explode(',', $v['imgUrl']);
		$out['t_'.$k]['img'] = $tArr[0];
		$out['t_'.$k]['cate'] = $v['category'];
		$out['t_'.$k]['donor'] = $v['donorInfo'];
		$out['t_'.$k]['summary'] = $v['description'];
	}
	echo json_encode($out);
}else if($_GET['f'] === 'deleteItem'){
	$db = new database();
	if($db->deleteGoodByName($_GET['name']))
		echo 'true';
}else if($_GET['f'] === 'getAuctionItems'){
	$db = new database();
	$cateId = intval($_GET['categoryId']);
	$r = $db->query('SELECT count(*) FROM `goods_info` WHERE `category` = (SELECT `name` FROM `goods_categories_info` WHERE `cateid` = '.$cateId.')');
	$t = $r->fetch_array();
	$count = $t[0];
	$out['totalPages'] = ceil($count/9);
	$out['currentPage'] = intval($_GET['currentPage']);
	$r = $db->selectGoodsByCategoryId($_GET['categoryId'],$out['currentPage']-1,9);
	$out['category']['name'] = $r[0]['category'];
	$j = 0;
	foreach ($r as $k => $v) {
		$out['category']['auctions'][$j]['id'] = $v['id'];
		$t = explode(',',$v['imgurl']);
		$i = 1;
		foreach ($t as $kk => $vv) {
			if($vv == '') continue;
			$img['path'.$i++] = $vv;
		}
		$out['category']['auctions'][$j]['img'] = $img;
		$out['category']['auctions'][$j++]['name'] = $v['name'];
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
	// var_dump($auctionMeta);
	$commentsMeta = $db->selectComments($gid);
	// var_dump($commentsMeta);
	$out['auctionItem'] = array(
		'name' => $auctionMeta['name'],
		'summary' => $auctionMeta['description'],
		'donor' => $auctionMeta['donorInfo'],
	);
	$imgArr = explode(',', $auctionMeta['imgUrl']);
	$i = 1;
	$out['auctionItem']['imgs']['path1'] = '';
	$out['auctionItem']['imgs']['path2'] = '';
	$out['auctionItem']['imgs']['path3'] = '';
	foreach ($imgArr as $v) {
		if($v == '') continue;
		$out['auctionItem']['imgs']['path'.$i++] = str_replace('thumb/', '', $v);
	}
	$i = 0;
	$commentsMeta = array();
	foreach ($commentsMeta as $v) {
		$user = json_decode($v['user_info_meta']);
		$out['comments'][$i]['user']['img'] = $user['headimgurl'];
		$out['comments'][$i]['user']['name'] = $v['name'];
		$out['comments'][$i++]['content'] = $v['content'];
	}
	echo json_encode($out);
}
?>