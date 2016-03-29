<!doctype html>
<html lang="en">
<head>
  <meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
  <title>拍品</title>
  <link rel="stylesheet" href="./style/style.css<?php echo '?t='.time(); ?>">
  <script src="http://apps.bdimg.com/libs/angular.js/1.4.6/angular.min.js"></script>
</head>
<body>
  <header>
    <nav>
      <img src="./image/logo.png" alt="爱心拍卖" class="logo">
      <div>共青团湖南商学院委员会<span>——拍品展示平台</span></div>
    </nav>
  </header>
  <div class="content" ng-app="category" ng-controller="showAuctionItems" id="ac">
    <h4>{{category.name}}</h4>
    <hr/>
    <div class="auction" ng-repeat="auction in category.auctions">
      <a href="./detail.php?gid={{auction.id}}">
        <img src="{{auction.img.path1}}" alt="{{auction.name}}">
        <span>{{auction.name}}</span>
      </a>
    </div>
    <hr/>
    <div class="pageTool">
      <input id="prev" type="button" value="上一页">
      <div class="jump">
        第<input id="cPage" type="text" value="{{currentPage}}">/<span>{{totalPages}}</span>页
      </div>
      <input id="next" type="button" value="下一页">
    </div>
    <script src="./script/getAuctionItems.js<?php echo '?t='.time(); ?>"></script>
</body>
</html>