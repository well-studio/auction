/**
   * @param {any,string,number,number} param type top bottom  
   * 
   * @return {boolean} 
   *================================
   * 验证参数类型，上下限。
   */
   function verifyParam(param,type,top,bottom){
    
    if(param !== [] && type === 'Array' ){
      return param instanceof Array;
    }

    if( !param || typeof param !== type){
      return false;
    }

    if((top && bottom) && (param < bottom || param > top)){
      return false;
    }

    return true;
   }


  /**
   *
   * @param {string,string[],string[],number,number} id linkUrls imgUrls time speed 
   * 
   * @return {boolean} 
   *===========================================================
   * 传入容器id，链接url数组，图片url数组，图片切换间隔，滑动速度。
   * 传入参数有误时报告错误并返回false。
   * time默认值为3000ms，下限为1000ms，上限为10000ms。
   * speed为切换到下页的帧数 默认值为50，下限为30，上限为80。
   */


  var slider = function(id,linkUrls,imgUrls,time,speed){
    if(!verifyParam(id, 'string')){
      console.error('参数错误！请传入正确的容器id！');
      return false; 
    }
    if(!verifyParam(linkUrls, 'Array')){
      console.error('参数错误！请传入正确格式的链接地址！');
      return false;
    }
    if(!verifyParam(imgUrls, 'Array')){
      console.error('参数错误！请传入正确格式的图片地址！');
      return false;
    }

    //初始化参数 time和speed是可选参数所以先赋值再检测
    //================================================
    var container = document.getElementById(id);
    var time = time || 3000;
    var speed = speed || 50;
    var isSwitching = false;

    if(!verifyParam(time, 'number',10000, 1000)){
      console.error('参数错误！请传入正确的切换时间！');
      return false;
    }
    if(!verifyParam(speed, 'number',80, 30)){
      console.error('参数错误！请传入正确的滑动速度！');
      return false;
    }


    
    //slider构造函数，其中pagesContainer保存的是图片链接的父节点对象，
    //currentPage为当前图片的序号。
    //====================================================
    function  MySlider(){
      this.currentPage = 0;
      this.inShift = null;
      this.container = container;
      this.dots  = [];
      this.pagesContainer = document.createElement('div');    
    }

    //slider的原型对象，两个insert函数创建图片
    //及切换工具节点并绑定事件，两个switch函数为
    //图片的切换函数。
    //==========================================
    MySlider.prototype = {

      init : function (){
        this.insertPageNodes();
        this.insertSwitchTool();
        this.dots[this.currentPage].style.backgroundColor = "#339999";
        autoSwitch();
        this.container.className = "ff-slider";
        
      },

      insertPageNodes : function (){

        var linkNode,imgNode,i;
        for(i = 0;i < imgUrls.length;i++){
          linkNode = document.createElement('a');
          imgNode = document.createElement('img');

          linkNode.href = linkUrls[i];
          linkNode.className = "link-node";
          imgNode.src = imgUrls[i];
          imgNode.alt = "图片加载失败";
          imgNode.style.height = this.container.offsetHeight + 'px';
          linkNode.style.width=this.container.offsetWidth+'px';
          linkNode.appendChild(imgNode);
          this.pagesContainer.appendChild(linkNode);
        }
        this.pagesContainer.style.left= 0;
        this.pagesContainer.style.position = 'absolute';
        this.pagesContainer.style.width = parseInt(this.container.offsetWidth)*parseInt(imgUrls.length)+'px';
  
        this.container.appendChild(this.pagesContainer);
      },

      insertSwitchTool : function(){
        var pageSwitchTool = document.createElement('div');
        pageSwitchTool.className = "tools";
        var i,dot;
        for(i = 0;i < imgUrls.length;i++){

          dot = document.createElement('div');
          dot.title = i;
          dot.onclick = pageSwitch;
          this.dots.push(dot);
          pageSwitchTool.appendChild(dot);
        }

        this.container.appendChild(pageSwitchTool);
      },
    };

    var mySlider = new MySlider();
    mySlider.init();
    
    function pageSwitch (){
        //currentPage改变前将当前dot的颜色变为普通色
        //=====================================
        mySlider.dots[mySlider.currentPage].style.backgroundColor = "gray";

        window.clearTimeout(mySlider.inShift);

        //检测是否在滑动中，防止自动滑动和点击滑动及连续点击滑动的冲突。
        //若在滑动则不做操作
        //=============================================================
        if(!isSwitching){
          isSwitching = true;
            //默认滑动距离为容器的宽度（即1张图片的宽度）
          var distance = -parseInt(mySlider.container.offsetWidth);
          var totalStep = 0;
          var pagesContainer = mySlider.pagesContainer;
          var step,switching;
          
          //若因点击而滑动，则距离为n倍
          if(event){
            distance = (parseInt(event.target.title) - mySlider.currentPage) * distance;
            mySlider.currentPage = parseInt(event.target.title);
            
          }else if(mySlider.currentPage == imgUrls.length-1){
            
            distance = -distance*(imgUrls.length-1);
            mySlider.currentPage = 0;

          }else{
            mySlider.currentPage++;
          }

          //滑动前将下一个dot颜色变为醒目色
          //===============================
          mySlider.dots[mySlider.currentPage].style.backgroundColor = "#339999";
          //取整便于计算，比较
          //速度值越大步幅越大
          //====================
          step = parseInt(distance/(100-speed));
          switching = window.setTimeout(slide, parseInt(1000/speed));    
        }

        function slide(){

          //step，totalStep，distance有正负
          if(Math.abs(totalStep) < Math.abs(distance)){
            //distanc不能整除step时，最后一步应加上totalStep和
            //distance的差值，不然totalStep的绝对值会大于distance
            //===================================================
            if(Math.abs(distance-totalStep)<Math.abs(step)){
              pagesContainer.style.left = parseInt(pagesContainer.style.left)+(distance-totalStep)+'px';
              totalStep = distance;
            }else{
              pagesContainer.style.left = parseInt(pagesContainer.style.left)+step+'px';
              totalStep += step;
            }
            switching = window.setTimeout(slide,1000/speed);
          }else{
            window.clearTimeout(switching);
            isSwitching = false;
            autoSwitch();
          } 
        }
      }

      function autoSwitch (){
        mySlider.inShift = window.setTimeout( function (){
          pageSwitch(); 
        } ,time);
       
      }
  };