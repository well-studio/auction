<!doctype html>
<html lang="en">
<head>
  <meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1">
  <title>拍品种类</title>
  <link rel="stylesheet" href="./style/style.css">
  <script src="http://apps.bdimg.com/libs/angular.js/1.4.6/angular.min.js"></script>
</head>
<body>
  <header>
    <nav>
      <img src="./image/logo.png" alt="爱心拍卖" class="logo">
      <div>共青团湖南商学院委员会<span>——拍品展示平台</span></div>
    </nav>
  </header>
  <div class="content" ng-app="heartAuction" ng-controller="showCategories">
    <h4>拍品种类</h4>
    <!--需要返回的数据：-->
    <!--所有分类（包括分类id，分类名，分类图片-->
    <div class="category" ng-repeat="category in categories">
      <a href="./auctionItems.php?cid={{category.id}}">
        <img src="{{category.img}}" alt="{{category.name}}">
        <span>{{category.name}}</span>
      </a>
    </div>
  </div>
  <script src="./script/getCategories.js"></script>
</body>
</html>