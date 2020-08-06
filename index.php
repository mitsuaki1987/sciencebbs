<?php
  $fp = fopen('data.csv', 'a+b');
  $submittime = date("c");
  $submitid = date("U");
  $uploadfile = $submitid . "_" . $_FILES['avatar']['name'];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['submittype'] === 'text') {
      fputcsv($fp, [$_POST['name'], $_POST['comment'], $_POST['submittype'], $submittime, $submitid]);
    }
    elseif ($_POST['submittype'] === 'image') {
      fputcsv($fp, [$_POST['name'], $uploadfile, $_POST['submittype'], $submittime, $submitid, $_FILES['avatar']['tmp_name']]);
      move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadfile);
      chmod($uploadfile, 0777);
    }
    elseif ($_POST['submittype'] === 'file') {
      fputcsv($fp, [$_POST['name'], $uploadfile, $_POST['submittype'], $submittime, $submitid, $_FILES['avatar']['tmp_name']]);
      move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadfile);
      chmod($uploadfile, 0777);
    }
    elseif ($_POST['submittype'] === 'freehand') {
      $canvas = $_POST['comment'];
      $canvas = base64_decode($canvas);
      $image = imagecreatefromstring($canvas);
      $uploadfile = date("U") . ".png";
      imagesavealpha($image, TRUE);
      imagepng($image , $uploadfile);
  
      fputcsv($fp, [$_POST['name'], $uploadfile, $_POST['submittype'], $submittime, $submitid]);
      chmod($uploadfile, 0777);
    }
    rewind($fp);
  }
  
  while ($row = fgetcsv($fp)) {
    $rows[] = $row;
  }
  
  fclose($fp);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" 
          content="text/html; charset=utf-8">
    <script type="text/x-mathjax-config">
      MathJax.Hub.Config({ tex2jax: { inlineMath: [['$','$'], ["\\(","\\)"]] }, displayAlign: "left" });
    </script>
    <script type="text/javascript"
            src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML">
    </script>
    <meta http-equiv="X-UA-Compatible" CONTENT="IE=EmulateIE7" />
    <title>BBS</title>
  </head>
  <body>
    <h1>BBS</h1>

    <p>&bsol; &lbrack; &bsol;&rbrack; : Independent LaTex formula</p>
    <p>$\$\$$ : Inline LaTex formula</p>
    
    <h2>Comments</h2>

    <?php if (!empty($rows)): ?>
      <ul>
        <?php foreach ($rows as $row): ?>
          <?php if ($row[2] == 'text'): ?>
            <li><?=$row[1]?> (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php elseif ($row[2] == 'freehand'): ?>
            <li><img src="<?=$row[1]?>" height="400" align="middle"> (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php elseif ($row[2] == 'image'): ?>
            <li><img src="<?=$row[1]?>" height="400" align="middle"> (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php elseif ($row[2] == 'file'): ?>
            <li><a href="<?=$row[1]?>"><?=$row[1]?></a> (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No comments</p>
    <?php endif; ?>

    <h2>Submit</h2>


    <input type="button" value="Submit" id="canvassubmit">
    <form id="submittype">
      <input type="radio" name="subtype" value="text" checked="checked">Text
      <input type="radio" name="subtype" value="image">Image
      <input type="radio" name="subtype" value="file">File
      <input type="radio" name="subtype" value="freehand">Freehand
    </form>
    <form enctype="multipart/form-data" id="submitting" action="" method="post">
      Name: <input type="text" name="name" id="submitname" value="">
      </br>
      Comment:
      </br>
      <textarea name="comment" id="comment" cols="40" rows="5" maxlength="1000" wrap="hard"></textarea>
      </br>
      File: <input type="file" id="file" name="filename">
      <br>
    </form>
    Free hand:
    <input type="button" value="Clear" onclick="clearCanvas();">
    <br>
    Width: <input type="text" id="width" value="600">
    Height: <input type="text" id="height" value="600">
    <input type="button" value="resize" onclick="resizeCanvas();">
    <br>
    <form id="linecolor">
      <input type="radio" name="color" value="black" checked="checked">Black
      <input type="radio" name="color" value="white">White
      <input type="radio" name="color" value="red">Red
      <input type="radio" name="color" value="green">Green
      <input type="radio" name="color" value="blue">Blue
    </form>
    <canvas id="canvas" width="600" height="600" style="border:solid black 1px;"></canvas>
    <pre id="log" style="border: 1px solid #ccc;"></pre>
    <script src="./draw.js"></script>
  </body>
</html>
