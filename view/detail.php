<?php
  require_once(__DIR__.'/../functions/common.func.php');
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
  <meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
  <link rel="stylesheet" href="./style/style.css">
  <script src="http://apps.bdimg.com/libs/angular.js/1.4.6/angular.min.js"></script>
  <link href="http://cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.css" rel="stylesheet">
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
      <div id="ff-slider" style="width:100%;height:14rem"></div>
      <div><div class="likeBox" style="float:right;margin-right:0.5em"><span id="lkcnt">{{auctionItem.likeCount}}</span><i id="lk" class="fa fa-thumbs-o-up" style="margin-left:0.5em;font-size:1.5em;"></i></div><span>简介</span><p>{{auctionItem.summary}}</p></div>
      <div><span>捐赠人</span><p>{{auctionItem.donor}}</p></div>
      <div><span>拍品拟拍价</span><p>{{auctionItem.price}}</p></div>
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
          <div>
            <span class="uname">{{comment.user.name}}</span>
            <div class="comment">{{comment.content}}</div>
          </div>
        </div>
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
          preOrder.style.display = "inherit";
        };
        back.onclick = function(){
          preOrder.style.display = "none";
        };


    (function(){
      var lkbtn = document.getElementById('lk');
      if(checkIsLiked()){
        lkbtn.className = 'fa fa-thumbs-up';
        lkbtn.style.color = '#E03D3D';
      }else{
        lkbtn.addEventListener('click',b,true);
      }
      function b(){
        var lkcnt = document.getElementById('lkcnt');
        lkcnt.innerHTML = parseInt(lkcnt.innerHTML) + 1;
        lkbtn.className="fa fa-thumbs-up";
        lkbtn.style.color = '#E03D3D';
        lkbtn.removeEventListener('click',b,true);
        var date = new Date();
        date.setTime(date.getTime() + 365*24*3600*1000);
        var gid = location.search.split('?').slice(-1).join('').split('=')[1];
        var likedList = getLikedList();
        if(likedList == ''){
          likedList = '|'+gid+'|';
        }else{
          likedList += gid+'|';
        }
        document.cookie = "likedList="+escape(likedList)+";expires="+date.toGMTString();
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function(){
          if(xhr.readyState == 4 && xhr.status >= 200 && xhr.status < 300 || xhr.status == 304){
            console.log('点赞成功');
          }
        }
        xhr.open('get','../f.php?f=clickLike&gid='+gid,true);
        xhr.send();
      }
      function getLikedList(){
        var arr;
        reg = /(^| )likedList=([^;]*)(;|$)/;
        if(arr=document.cookie.match(reg))
          return unescape(arr[2]);
        else
          return '';
      }
      function checkIsLiked(){
        var gid = location.search.split('?').slice(-1).join('').split('=')[1];
        var exp = new RegExp('\\|'+gid+'\\|');
        return exp.test(getLikedList());
      }
    })();
  </script>
  <script src="script/slide.js"></script>
  <script type="text/javascript" src="./script/getDetail.js"></script>
</body>
</html>