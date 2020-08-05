<?php
  $fp = fopen('data.csv', 'a+b');
  $submittime = date("c");
  $uploadfile = date("U") . "_" . $_FILES['filename']['name'];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['submittype'] === 'text') {
      fputcsv($fp, [$_POST['name'], $_POST['comment'], $_POST['submittype'], $submittime]);
    }
    elseif ($_POST['submittype'] === 'latex') {
      fputcsv($fp, [$_POST['name'], $_POST['comment'], $_POST['submittype'], $submittime]);
    }
    elseif ($_POST['submittype'] === 'image') {
      fputcsv($fp, [$_POST['name'], $uploadfile, $_POST['submittype'], $submittime, $_FILES['filename']['tmp_name'], $uploadfile]);
      move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile);
      chmod($uploadfile, 0777);
    }
    elseif ($_POST['submittype'] === 'file') {
      fputcsv($fp, [$_POST['name'], $uploadfile, $_POST['submittype'], $submittime, $_FILES['filename']['tmp_name'], $uploadfile]);
      move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile);
      chmod($uploadfile, 0777);
    }
    elseif ($_POST['submittype'] === 'freehand') {
      $canvas = $_POST['comment'];
      $canvas = base64_decode($canvas);
      $image = imagecreatefromstring($canvas);
      $uploadfile = date("U") . ".png";
      imagesavealpha($image, TRUE);
      imagepng($image , $uploadfile);
  
      fputcsv($fp, [$_POST['name'], $uploadfile, $_POST['submittype'], $submittime, $_FILES['filename']['tmp_name'], $uploadfile]);
      move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile);
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
      MathJax.Hub.Config({ tex2jax: { inlineMath: [['$','$'], ["\\(","\\)"]] } });
    </script>
    <script type="text/javascript"
            src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML">
    </script>
    <meta http-equiv="X-UA-Compatible" CONTENT="IE=EmulateIE7" />
    <title>BBS</title>
  </head>
  <body>
    <h1>BBS</h1>

    <h2>Comments</h2>
    <?php if (!empty($rows)): ?>
      <ul>
        <?php foreach ($rows as $row): ?>
          <?php if ($row[2] == 'text'): ?>
            <li><?=$row[1]?> (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php elseif ($row[2] == 'latex'): ?>
            <li>\[ <?=$row[1]?> \] (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php elseif ($row[2] == 'freehand'): ?>
            <li><img src="<?=$row[1]?>" height="400"> (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php elseif ($row[2] == 'image'): ?>
            <li><img src="<?=$row[1]?>" height="400"> (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php elseif ($row[2] == 'file'): ?>
            <li><a href="<?=$row[1]?>"><?=$row[1]?></a> (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No comments</p>
    <?php endif; ?>

    <h2>Submit</h2>
    
    <form enctype="multipart/form-data" action="" method="post">
      Name: <input type="text" name="name" value="">
      </br>
      Comment:
      </br>
      <textarea name="comment" cols="30" rows="3" maxlength="80" wrap="hard" placeholder="Within 80 latters">
      </textarea>
      </br>
      File: <input type="file" name="filename">
      <br>
      <input type="radio" name="submittype" value="text" checked="checked">Text
      <input type="radio" name="submittype" value="latex">Latex
      <input type="radio" name="submittype" value="freehand">FreeHand
      <input type="radio" name="submittype" value="image">Image
      <input type="radio" name="submittype" value="file">File
      <input type="submit" value="Submit">
    </form>
    <hr>
    Free hand:
    <br>
    <canvas id="canvas" width="600" height="600" style="border:solid black 1px;"></canvas>
    <br>
    Name: <input type="text" name="namefreehand" id="freehandname" value="">
    <input type="button" value="Clear" onclick="clearCanvas();">
    <input type="button" value="Submit Freehand" id="canvassubmit">
    Log: <pre id="log" style="border: 1px solid #ccc;"></pre>
    <script src="./draw.js"></script>
  </body>
</html>
