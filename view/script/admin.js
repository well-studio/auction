var itemAdd = document.getElementById('itemAdd'),
    itemView = document.getElementById('itemView'),
    passAdmin = document.getElementById('passAdmin');
var add = document.getElementById('add'),
    view = document.getElementById('view'),
    change = document.getElementById('change');
var app = angular.module('admin',[]);
app.controller('adminAuctionItems',function($scope){
    $scope.items = null;
});
//加载页面时验证哈希以决定显示的内容
valiHash();

add.onclick = function(){
  //因为会先触发onclick事件再触发默认事件,此时哈希值还未改变
  //所以需要先传入将要改变的哈希值
  valiHash('#'+this.href.split('#').slice(-1));
}
view.onclick = function(){
  valiHash('#'+this.href.split('#').slice(-1));
  getData();
}
change.onclick = function(){
  valiHash('#'+this.href.split('#').slice(-1));
}

function valiHash(hash){

  var hash = hash||location.hash;
  if(hash === '#itemView'){
    itemView.style.display = 'inherit';
    itemAdd.style.display = passAdmin.style.display = 'none';
    getData();
  }else if(hash === '#passAdmin'){
    passAdmin.style.display = 'inherit';
    itemAdd.style.display = itemView.style.display = 'none';
  }else{
    itemAdd.style.display = 'inherit';
    itemView.style.display = passAdmin.style.display = 'none';
  }
}

function getData(){
  var xhr = new XMLHttpRequest();
  var items;
  xhr.onreadystatechange = function(){
    if(xhr.readyState === 4){
      if(xhr.status >= 200 && xhr.status < 300 || xhr.status == 304){
         items = JSON.parse(xhr.responseText); 
         var itemView = document.getElementById('itemView');
         var scope = angular.element(itemView).scope();
         scope.$apply(function(){
            scope.items = items;
         });
      }else{
         console.error("请求失败：" + xhr.status);
      }
    }
  }
  //这里的path需要设置！！
  xhr.open('get','../f.php?f=getAllItem',true);
  xhr.send(null);
}

function deleteItem(itemName){
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function(){
    if(xhr.readyState === 4){
      if(xhr.status >= 200 && xhr.status < 300 || xhr.status == 304){
         if(xhr.responseText === 'true'){
            console.info('已删除');
            document.getElementById('itemName').style.display = 'none';
         }else{
            console.error('出错！未删除!');
         }
      }else{
         console.error("请求失败：" + xhr.status);
      }
    }
  }
  //这里的path需要设置！！
  xhr.open('delete','../f.php?f=deleteItem&name='+itemName,true);
  xhr.send(null);
}
function showBidder(){
  var bidder = event.target.parentNode.parentNode.getElementsByTagName('table')[0];
  var wrap = document.getElementById('wrap');
  if(wrap.style.display==="none" && bidder.style.display==="none"){
     wrap.style.display = bidder.style.display = "";
  }
  wrap.onclick = function(){
    this.style.display = "none";
    bidder.style.display = "none";
  }
}