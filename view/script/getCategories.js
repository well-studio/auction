
var app = angular.module('heartAuction', []);

app.controller('showCategories', function($scope) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function(){
     if(xhr.readyState == 4){
       if(xhr.status >= 200 && xhr.status < 300 || xhr.status == 304){
         $scope.$apply(function(){
            $scope.categories = JSON.parse(xhr.responseText);
         });
       }else{
         console.error("请求失败：" + xhr.status);
       }
     }
    };
    //这里的path需要设置！！
    xhr.open('get','../f.php?f=getCategories',true);
    xhr.send(null);
});