<?php
  $fp = fopen('data.csv', 'a+b');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fputcsv($fp, [$_POST['name'], $_POST['comment'], $_POST['submittype']]);
    rewind($fp);
  }
  while ($row = fgetcsv($fp)) {
    $rows[] = $row;
  }
  fclose($fp);
?>
<!DOCTYPE html>
<html lang="ja">
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
            <li><?=$row[1]?> (<?=$row[2]?>) (by <?=$row[0]?>)</li>
          <?php elseif ($row[2] == 'latex'): ?>
            <li>\[ <?=$row[1]?> \] (<?=$row[2]?>) (by2  <?=$row[0]?>)</li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No comments</p>
    <?php endif; ?>

    <h2>Submit</h2>
    
    <form action="" method="post">
      Name: <input type="text" name="name" value="">
      </br>
      Comment:
      </br>
      <textarea name="comment" cols="30" rows="3" maxlength="80" wrap="hard" placeholder="Within 80 latters">
      </textarea>
      </br>
      File: <input type="file" name="filename">
      <br>
      Free hand:
      </br>
      <canvas id="canvas" width="600" height="600" style="border:solid black 1px;"></canvas>
      <script src="./draw.js"></script>
      <button onclick="startup()">Initialize</button>
      <br>
      <input type="radio" name="submittype" value="text" checked="checked">Text
      <input type="radio" name="submittype" value="latex">Latex
      <input type="radio" name="submittype" value="freehand">FreeHand
      <input type="radio" name="submittype" value="image">Image
      <input type="radio" name="submittype" value="file">File
      <input type="submit" value="Submit">
    </form>
    <hr>
  </body>
</html>
