require.config({
  baseUrl:'script/slide/libs',
  paths:{
   slider:'slider'
  }
});
require(['slider'],function(slider){
  //传入容器id，链接及图片地址，切换时间（可选）和滑动速度（可选）
  //================================================================
  slider(
    'ff-slider',
    ['#','#','#'],
    ['https://ss2.bdstatic.com/70cFvnSh_Q1YnxGkpoWK1HF6hhy/it/u=1023930876,2577057362&fm=111&gp=0.jpg',
     'https://ss2.bdstatic.com/70cFvnSh_Q1YnxGkpoWK1HF6hhy/it/u=1023930876,2577057362&fm=111&gp=0.jpg',
     'https://ss2.bdstatic.com/70cFvnSh_Q1YnxGkpoWK1HF6hhy/it/u=1023930876,2577057362&fm=111&gp=0.jpg'],
     null,
     80
  );
});