<?php
  $r = scandir(__DIR__.'/content/img');
  foreach ($r as $v) {
    if($v == '.' || $v == '..')
      continue;
    $imgArr[] = 'content/img/'.$v;
  }
  $imgArrJson = json_encode($imgArr);
  $title = file_get_contents(__DIR__.'/content/title');
  $content = file_get_contents(__DIR__.'/content/content');
  $actIntro = '<h1>'.$title.'</h1>'.$content;
?>
<!doctype html>
<html lang="en">
<head>
  <meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
  <title>首页</title>
  <link rel="stylesheet" href="./style/style.css">
  <link rel="stylesheet" href="./style/slide/main.css">
  <script src="./script/slide.js"></script>
</head>
<body>
 <div class="idx">
   <div id="ff-slider" style="width:100%;height:16rem;"></div>
   <section>
     <article>
       <h4>活动介绍</h4>
       <p>
       <?php echo $actIntro ?>
       </p>
     </article>
   </section>
   <hr>
   <div class="idx-link"><a href="categories.php">查看拍品</a></div>
 </div>
 <script>
   slider(
    'ff-slider',
    ['#','#','#'],
    <?php echo $imgArrJson ?>,
    null,
    80
    );
 </script>
</body>
</html>
