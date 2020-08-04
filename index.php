<?php
  $fp = fopen('data.csv', 'a+b');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  fputcsv($fp, [$_POST['name'], $_POST['comment']]);
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
    <title>BBS</title>
  </head>
  <body>
    <h1>BBS</h1>
    <section>
    </section>
    <section class="toukou">
      <h2>Comments</h2>
      <?php if (!empty($rows)): ?>
      <ul>
        <?php foreach ($rows as $row): ?>
        <li><?=$row[1]?> (<?=$row[0]?>)</li>
        <?php endforeach; ?>
      </ul>
      <?php else: ?>
      <p>No comments</p>
      <?php endif; ?>
    </section>
      <h2>Submit</h2>
      <form action="" method="post">
        <div class="name">
          <span class="label">Name:</span><input type="text" name="name" value="">
        </div>
        <div class="honbun">
          <span class="label">Comment:</span>
          <textarea name="comment" cols="30" rows="3" maxlength="80" wrap="hard" placeholder="Within 80 latters">
          </textarea>
        </div>
        <canvas id="canvas" width="600" height="600" style="border:solid black 1px;"></canvas>
        <pre id="log" style="border: 1px solid #ccc;"></pre>
        <script src="./draw.js"></script>
        <input type="submit" value="Submit">
      </form>
  </body>
</html>
