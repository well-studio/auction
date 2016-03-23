
    var app = angular.module("auctionItem",[]);


    app.controller("showDetail",function($scope){
      var xhr = new XMLHttpRequest();
      var id = location.search.split('?').slice(-1).join('').split('=')[1];
      xhr.onreadystatechange = function(){
        if(xhr.readyState == 4){
          if(xhr.status >= 200 && xhr.status < 300 || xhr.status == 304){
            var returnValue = JSON.parse(xhr.responseText);

            $scope.$apply(function(){
              $scope.auctionItem = returnValue.auctionItem;
              $scope.comments =  returnValue.comments;
            });
          }else{
            console.error("请求失败：" + xhr.status);
          }
        }
      }
      //这里的path需要设置！！
      xhr.open('get','../f.php?f=getDetail&id='+id,true);
      xhr.send(null);

    });