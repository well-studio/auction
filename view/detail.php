<?php
  require_once(__DIR__.'/../functions/common.func.php');
  var_dump($_COOKIE);
  var_dump($_GET);
  var_dump($_POST);
  if(isset($_POST['fun']) && $_POST['fun'] == 'comment'){
    $db = new database();
    if(!empty($_POST['comment'])){
      $db->insertComment($_GET['gid'], htmlentities($_POST['comment']));
    }else{
      $errMsg = '评论内容不能为空';
    }
  }
  if(isset($_POST['fun']) && $_POST['fun'] == 'auction'){
    if(preg_match('/^1[34578]\d{9}$/', $_POST['phoneNumber'])){
      if(preg_match('/^\d+(\.?\d+)?$/', $_POST['myPrice'])){
        if(!empty($_POST['name'])){
          $maxInfo = getMaxPriceInfo($_GET['gid']);
          if($_POST['myPrice'] > $maxInfo['price']){
            $db = new database();
            $db->insertAuctionPrice($_POST['myPrice'], $_POST['name'], $_POST['phoneNumber'], $_GET['gid']);
          }else{
            $errMsg = '不能低于或等于最大竞拍价';
          }
        }else{
          $errMsg = '姓名不能为空';
        }
      }else{
        $errMsg = '价钱格式不正确';
      }
    }else{
      $errMsg = '手机号码格式不正确';
    }
  }
?><!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>拍品详情</title>
  <link rel="stylesheet" href="./style/style.css">
  <script src="http://apps.bdimg.com/libs/angular.js/1.4.6/angular.min.js"></script>
  <link rel="stylesheet" href="./style/slide/main.css">
</head>
<body>
  <header>
    <nav>
      <img src="./image/logo.png" alt="爱心拍卖" class="logo">
      <div>共青团湖南商学院委员会<span>——拍品展示平台</span></div>
    </nav>
  </header>
  <!--需要返回的数据：-->
  <!--拍品信息（name,img,summary,donor）-->
  <!--评论：评论人（头像，昵称），评论内容-->
  <div id="auctionItem" class="content" ng-app ="auctionItem" ng-controller="showDetail">
    <h4>{{auctionItem.name}}</h4>
    <hr/>
    <div class="detail">
      <div id = "ff-slider" style = "width:100%;height:8rem"></div>
      <div>简介：<p>{{auctionItem.summary}}</p></div>
      <div>捐赠人：<span>{{auctionItem.donor}}</span></div>
      <div>拍品拟拍价：<span>{{auctionItem.price}}</span></div>
      <div><input type="button" value="我要抢拍" id="order"></div>
    </div>
    <?php if(isset($errMsg)){ ?>
    <div class="errMsgBox">
      <?php echo $errMsg ?>
    </div>
    <?php }?>
    <div class="comments">
      <h4>评论</h4>
      <hr>
      <form action="" method="post">
        <input type="hidden" name="fun" value="comment">
        <fieldset>
          <textarea name="comment" id="comm"></textarea>
          <input type="submit" value="留言">
        </fieldset>
      </form>
      <div class="user" ng-repeat="comment in comments">
        <div>
          <img src="{{comment.user.img}}" alt="{{comment.user.name}}">
          <span class="uname">{{comment.user.name}}</span>
        </div>
        <div class="comment">{{comment.content}}</div>
      </div>
    </div>
    <div id="preOrder">
    <form action="" method="post" >
      <div id="back"><span>-</span></div>
      <fieldset>
        <legend>我要抢拍</legend>
        <input type="hidden" name="fun" value="auction">
        <input type="text" name="name" placeholder="姓名"><br/>
        <input type="text" name="phoneNumber" placeholder="电话"><br/>
        <input type="text" name="myPrice" placeholder="意向拍价"><br/>
        <div>当前最高价:<span>{{auctionItem.topPrice}}</span></div>
        <input type="submit" value="确定">
      </fieldset>
    </form>
  </div>
  </div>

  <script type="text/javascript">
    var preOrder = document.getElementById('preOrder'),
        order = document.getElementById('order'),
        back = document.getElementById('back');

        order.onclick = function(){
          preOrder.style.display = "inherit"
        }
        back.onclick = function(){
          preOrder.style.display = "none" 
        }
  </script>
  <script src="script/slide.js"></script>
  <script type="text/javascript" src="./script/getDetail.js"></script>
</body>
</html>