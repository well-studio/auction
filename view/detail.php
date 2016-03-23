<!doctype html>
<html lang="en">
<head>
  <meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1">
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
  <div class="content" ng-app ="auctionItem" ng-controller="showDetail">
    <h4>{{auctionItem.name}}</h4>
    <hr/>
    <div class="detail">
      <div id = "ff-slider" style = "width:100%;height:8rem"></div>
      <div>简介：<p>{{auctionItem.summary}}</p></div>
      <div>捐赠人：<span>{{auctionItem.donor}}</span></div>
    </div>
    <div class="comments">
      <h4>评论</h4>
      <hr>
      <div class="user" ng-repeat="comment in comments">
        <div>
          <img src="{{comment.user.img}}" alt="{{comment.user.name}}">
          <span class="uname">{{comment.user.name}}</span>
        </div>
        <div class="comment">{{comment.content}}</div>
      </div>
    </div>
  </div>
  <script type="text/javascript" src="./script/getDetail.js"></script>
  <script data-main="./script/slide/main" src="script/slide/require.js"></script>
</body>
</html>