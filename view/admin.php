<?php
/* 用来检测是否登录 */
session_start();
if(!$_SESSION['admin']){
  header('Location: ./adminLogin.php');
}



require_once(__DIR__.'/../class/database.class.php');
require_once(__DIR__.'/../class/resizeimage.class.php');
$flag = true;
$out = '';
if(isset($_POST['name']) && $_POST['name']){
  $name = htmlspecialchars($_POST['name']);
  $cate = htmlspecialchars($_POST['cate']);
  $dono = htmlspecialchars($_POST['donor']);
  $pric = htmlspecialchars($_POST['price']);
  $summ = htmlspecialchars($_POST['summary']);
  $imgUrl = '';
  if(mb_strlen($name,'utf8')<7){
    $time = time();
    for($i=1;$i<4;$i++){
      if($_FILES['img'.$i]['error']) continue;
      if(!preg_match('/jpg|png|gif/', $_FILES['img'.$i]['name'], $r)) {
        $flag = false;
        $out = '图片'.$_FILES['img'.$i]['name'].'格式不正确';
        break;
      }
      $url = 'image/auction/'.$time.'_'.$i.'.'.$r[0];
      $thumbUrl = 'image/auction/thumb/'.$time.'_'.$i.'.'.$r[0];
      $imgUrl .= $thumbUrl.',';
      file_put_contents($url, file_get_contents($_FILES['img'.$i]['tmp_name']));
      new resizeimage($url, '200', '200', '0', $thumbUrl);
    }
  }else{
    $flag = false;
    $out = '名称长度不能超过6';
  }
  $db = new database();
  $name = $db->real_escape_string($name);
  $r = $db->query("SELECT 1 FROM `goods_info` WHERE `name` = '{$name}'");
  if($r->fetch_assoc()) {
    $flag = false;
    $out = '不能有重复的名称';
  }
  if($flag && $db->insertGood($name,$cate, $summ, $dono,101,$imgUrl)){
    $stmt = $db->prepare('UPDATE `goods_info` SET `auction_info` = ? WHERE `name` = ?');
    $stmt->bind_param('ss',$a,$b);
    $rb[] = array(
      'price' => floatval($pric),
      'user' => 'admin',
      'phoneNumber' => '00000000000'
    );
    $a = json_encode($rb); $b = $name;
    $stmt->execute();
    $stmt->close();
    $out = '添加成功';
  }else{
    $out .= ',添加失败';
  }
}
if(isset($_POST['oldPass'])){
  $db = new database();
  if($db->checkUserPass('admin',$_POST['oldPass'])){
    if($_POST['pass'] === $_POST['confirm']){
      $out = '修改成功！';
      $db->updateUserPass('1','admin',$_POST['pass']);
    }else{
      $out = '两次密码不一样';
    }
  }else{
    $out = '旧密码错误！';
  }
}
?><!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>后台管理</title>
  <link rel="stylesheet" href="./style/admin.css">
  <script src="http://apps.bdimg.com/libs/angular.js/1.4.6/angular.min.js"></script>
</head>
<body>
  <header>
    <nav>
      <img src="./image/logo.png" alt="爱心拍卖" class="logo">
      <div>共青团湖南商学院委员会<span>——拍品展示平台</span></div>
    </nav>
  </header>
  <div class="content">
    <div class="sidebar">
      <ul class="mal">
        <li>拍品管理</li>
        <li>
          <ul >
            <li><a id="add" href="#itemAdd">添加拍品</a></li>
            <li><a id="view" href="#itemView">查看拍品</a></li>
          </ul>
        </li>
        <li><a id="change" href="#passAdmin">设置管理密码</a></li>
      </ul>
    </div>
    <div class="admin" >
      <div id="itemAdd">
        <h4>添加拍品</h4>
        <hr>
        <form  action="" method="post" enctype="multipart/form-data">
          <fieldset class="formField">
            <label for="name">&emsp;名称：</label><input type="text" name="name" value="<?php if(isset($name)) echo $name ?>"><br/>
            <label for="cate">&emsp;类别：</label><input type="text" name="cate" value="<?php if(isset($cate)) echo $cate ?>"><br/>
            <label for="donor">捐赠人：</label><input type="text" name="donor" value="<?php if(isset($dono)) echo $dono ?>"><br/>
            <label for="price">拟拍价：</label><input type="text" name="price" value="<?php if(isset($pric)) echo $pric ?>"><br/>
            <label for="summary">&emsp;简介：</label>
            <textarea name="summary" id="summary"><?php if(isset($summ)) echo $summ ?></textarea><br/>
            <label for="img1">&emsp;图片1：</label><input name="img1" type="file">
            <label for="img2">&emsp;图片2：</label><input name="img2" type="file">
            <label for="img3">&emsp;图片3：</label><input name="img3" type="file">
            <hr>
            <span><?php if(isset($out)) echo $out ?></span>
            <input type="submit" value="添加">
          </fieldset>
        </form>
      </div>
      <div id="itemView" style="display:none" ng-app="admin" ng-controller="adminAuctionItems">
        <h4>查看拍品</h4>
        <hr>
        <table>
          <thead>
            <tr>
              <th>图像</th>
              <th>名称</th>
              <th>类别</th>
              <th>捐赠人</th>
              <th>简介</th>
              <th>拟拍价</th>
              <th>竞拍人</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="item in items" id="{{item.name}}">
              <td><img src="{{item.img}}" alt="{{item.name}}"></td>
              <td>{{item.name}}</td>
              <td>{{item.cate}}</td>
              <td>{{item.donor}}</td>
              <td>{{item.summary}}</td>
              <td>{{item.price}}</td><!--改动的地方-->
              <td><input type="button" value="竟拍人" onclick = "showBidder()"></a></td>
              <!--改动的地方-->
              <td><input type="button" value="删除" onclick = "deleteItem({{item.name}})"></td>
              <!--改动的地方-->
              <td>
                  <table class="bidders" style="display:none">
                    <thead>
                      <tr>
                        <th>姓名</th>
                        <th>电话</th>
                        <th>竞拍价</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr ng-repeat="bidder in item.auctionInfos" id="{{bidder.name}}">
                        <td>{{bidder.user}}</td>
                        <td>{{bidder.phoneNumber}}</td>
                        <td>{{bidder.price}}</td>
                      </tr>
                    </tbody>
                  </table>
              </td>
              <!--改动的地方-->
            </tr>
          </tbody>
        </table>
      </div>
      <div id="passAdmin" style="display:none">
        <h4>密码设置</h4>
        <hr>
        <form action="" method="post">
          <fieldset>
            <label for="oldPass">&emsp;旧密码：</label><input type="password" name="oldPass" value="<?php if(isset($_POST['oldPass'])) echo $_POST['oldPass'] ?>"><br/>
            <label for="pass">&emsp;新密码：</label><input type="password" name="pass" value="<?php if(isset($_POST['pass'])) echo $_POST['pass'] ?>"><br/>
            <label for="confirm">确认密码：</label><input type="password" name="confirm" value="<?php if(isset($_POST['confirm'])) echo $_POST['confirm'] ?>"><br/>
            <hr>
            <span><?php if(isset($out)) echo $out ?></span>
            <input type="submit" value="修改">
          </fieldset>
        </form>
      </div>
    </div>
  </div>
  <!--改动的地方-->
  <div id="wrap" style="display:none"></div>
  <script src="./script/admin.js"></script>

</body>
</html>