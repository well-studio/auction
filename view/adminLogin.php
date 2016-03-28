<?php
  require_once(__DIR__.'/../class/database.class.php');
  session_start();
  $_SESSION['admin'] = false;
  if(isset($_POST['name']) && isset($_POST['pass'])){
    $name = $_POST['name'];
    $pass = $_POST['pass'];
    $db = new database();
    if($db->checkUserPass($name,$pass)){
      $_SESSION['admin'] = true;
      header('Location: ./admin.php');
    }else{
      $out = '用户名或密码错误';
    }
  }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>后台登录</title>
  <link rel="stylesheet" href="./style/admin.css">
  <script src="http://apps.bdimg.com/libs/angular.js/1.4.6/angular.min.js"></script>
</head>
<body>
  <header>
    <nav>
      <img src="./image/logo.png" alt="爱心拍卖" class="logo">
      <div>共青团湖南商学院委员会<span>——拍品展示平台</span></div>
    </nav>
  </header>
  <div style="max-width: 300px;margin: 0 auto">
    <form action="" method="post">
      <label for="name">用户名:</label><input type="text" name="name" value="<?php if(isset($name)) echo $name ?>"><br/>
      <label for="name">　密码:</label><input type="password" name="pass" value="<?php if(isset($pass)) echo $name ?>"><br/>
      <hr>
      <span><?php if(isset($out)) echo $out ?></span>
      <input type="submit" value="登录">
    </form>
  </div>
</body>
</html>