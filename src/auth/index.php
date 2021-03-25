<?php
  $fp = fopen('data.csv', 'a+b');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submittime = date("c");
    $submitid = date("U");

    $submittype = $_POST['submittype'];
    $name = $_SERVER['PHP_AUTH_USER'];

    $thisurl = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

    if ($submittype === 'text' or $submittype === 'file' or $submittype === 'image' or $submittype === 'freehand'){
      $rssxml = fopen('rss.xml', 'w');
      fwrite($rssxml, "<?xml version='1.0' encoding='UTF-8'?>\n");
      fwrite($rssxml, "<rss version='2.0'>\n");
      fwrite($rssxml, "  <channel>\n");
      fwrite($rssxml, "    <title>BBS</title>\n");
      fwrite($rssxml, "    <link>" . $thisurl . "</link>\n");
      fwrite($rssxml, "    <description></description>\n");
      fwrite($rssxml, "    <item>\n");
      fwrite($rssxml, "      <title>" . $name . " wrote a comment</title>\n");
      fwrite($rssxml, "      <link>" . $thisurl . "</link>\n");
      fwrite($rssxml, "      <guid>" . $thisurl . "#". $submitid . "</guid>\n");
      fwrite($rssxml, "      <description></description>\n");
      fwrite($rssxml, "      <pubDate>" . $submittime . "</pubDate>\n");
      fwrite($rssxml, "    </item>\n");
      fwrite($rssxml, "  </channel>\n");
      fwrite($rssxml, "</rss>\n");
      fclose($rssxml);
    }

    if ($submittype === 'text') {
      $comment = $_POST['comment'];
      fputcsv($fp, [$name, $comment, $submittype, $submittime, $submitid]);
    }
    elseif ($submittype === 'file' or $submittype === 'image') {
      $uploadfile = $submitid . "_" . $_FILES['avatar']['name'];
      $tmp_name = $_FILES['avatar']['tmp_name'];
      fputcsv($fp, [$name, $uploadfile, $submittype, $submittime, $submitid, $tmp_name]);
      move_uploaded_file($tmp_name, $uploadfile);
      chmod($uploadfile, 0777);
    }
    elseif ($submittype === 'freehand') {
      $canvas = $_POST['comment'];
      $canvas = base64_decode($canvas);
      $image = imagecreatefromstring($canvas);
      $uploadfile = date("U") . ".png";
      imagesavealpha($image, TRUE);
      imagepng($image , $uploadfile);
  
      fputcsv($fp, [$name, $uploadfile, $submittype, $submittime, $submitid]);
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
            src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-AMS_HTML">
    </script>
    <meta http-equiv="X-UA-Compatible" CONTENT="IE=EmulateIE7" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">
$(function() {
  function makePreview() {
    input = $('#comment').val();
    $('#preview').html(input);
    MathJax.Hub.Queue(["Typeset",MathJax.Hub,"preview"]);
  }
  $('body').keyup(function(){makePreview()});
  $('body').bind('updated',function(){makePreview()});
  makePreview();
});
</script>
    <title>BBS</title>
  </head>
  <body>
    <h1>BBS</h1>

    <ul>
      <li><code>\[ \]</code> : Independent LaTex formula</li>
      <li><code>$ $</code> : Inline LaTex formula</li>
      <li><code>&lt;ul&gt;&lt;li&gt;&lt;/li&gt;&lt;/ul&gt;</code> : Itemization</li>
      <li><code>&lt;br&gt;</code> : New line</li>
      <li><a href="./rss.xml">RSS</a></li>
    </ul>
    
    <h2>Comments</h2>

    <?php if (!empty($rows)): ?>
      <ol>
        <?php foreach ($rows as $row): ?>
          <?php if ($row[2] == 'text'): ?>
            <li><?=$row[1]?> (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php elseif ($row[2] == 'freehand' or $row[2] == 'image'): ?>
            <li><img src="<?=$row[1]?>" height="400" align="middle"> (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php elseif ($row[2] == 'file'): ?>
            <li><a href="<?=$row[1]?>"><?=$row[1]?></a> (<?=$row[2]?> by <?=$row[0]?> at <?=$row[3]?>)</li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ol>
    <?php else: ?>
      <p>No comments</p>
    <?php endif; ?>

    <h2>Submit</h2>

    <input type="button" value="Submit" onclick="post()">
    <form id="submittype">
      <input type="radio" name="subtype" value="text" checked="checked">Text
      <input type="radio" name="subtype" value="image">Image
      <input type="radio" name="subtype" value="file">File
      <input type="radio" name="subtype" value="freehand">Freehand
    </form>
    <form enctype="multipart/form-data" id="submitting" action="" method="post">
      Comment:
      </br>
      <textarea name="comment" id="comment" cols="40" rows="5" maxlength="1000" wrap="hard"></textarea>
      </br>
      Preview:
      </br>
      <div id="preview"></div>
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
<script type="text/javascript">
function startup() {
  var el = document.getElementById("canvas");
  el.addEventListener("touchstart", touch_start, false);
  el.addEventListener("touchmove", touch_move, false);
  el.addEventListener("mousedown", mouse_down, false);
  el.addEventListener("mousemove", mouse_move, false);
  el.addEventListener("mouseup", mouse_up, false);
}

document.addEventListener("DOMContentLoaded", startup);

let touch0x = 0.0;
let touch0y = 0.0;

function touch_start(evt) {
  evt.preventDefault();
  var touches = evt.changedTouches;
  for (i = 0; i < touches.length; i++) {
    if (touches[i].identifier == 0) {
      touch0x = touches[i].clientX;
      touch0y = touches[i].clientY;
    }
  }
}

function touch_move(evt) {
  evt.preventDefault();
  var touches = evt.changedTouches;
  var el = document.getElementById("canvas");
  var ctx = el.getContext("2d");
  var rect = el.getBoundingClientRect()
  var linecolor = document.getElementById('linecolor').color.value;

  for (i = 0; i < touches.length; i++) {
    if (touches[i].identifier == 0) {
      ctx.beginPath();
      ctx.moveTo(touch0x            - rect.left, touch0y            - rect.top);
      ctx.lineTo(touches[i].clientX - rect.left, touches[i].clientY - rect.top);
      ctx.lineWidth = 4;
      ctx.strokeStyle = linecolor;
      ctx.stroke();
      touch0x = touches[i].clientX;
      touch0y = touches[i].clientY;
    }
  }
}
let isDrawing = false;

function mouse_down(evt){
  touch0x = evt.offsetX;
  touch0y = evt.offsetY;
  isDrawing = true;
};

function mouse_move(evt) {
  var el = document.getElementById("canvas");
  var ctx = el.getContext("2d");
  var linecolor = document.getElementById('linecolor').color.value;

  if (isDrawing === true) {
    ctx.beginPath();
    ctx.moveTo(touch0x, touch0y);
    ctx.lineTo(evt.offsetX, evt.offsetY);
    ctx.lineWidth = 4;
    ctx.strokeStyle = linecolor;
    ctx.stroke();
    touch0x = evt.offsetX;
    touch0y = evt.offsetY;
  }
};

function mouse_up(evt) {
    var el = document.getElementById("canvas");
    var ctx = el.getContext("2d");
    var linecolor = document.getElementById('linecolor').color.value;

    if (isDrawing === true) {
      ctx.beginPath();
      ctx.moveTo(touch0x, touch0y);
      ctx.lineTo(evt.offsetX, evt.offsetY);
      ctx.lineWidth = 4;
      ctx.strokeStyle = linecolor;
      ctx.stroke();
      touch0x = 0;
      touch0y = 0;
    isDrawing = false;
  }
};
function clearCanvas() {
  var el = document.getElementById("canvas");
  var ctx = el.getContext("2d");

  ctx.clearRect(0, 0, el.width, el.height);
}
function resizeCanvas() {
  var el = document.getElementById("canvas");
  var width = document.getElementById('width').value;
  var height = document.getElementById('height').value;

  el.setAttribute("width", width);
  el.setAttribute("height", height);
}
function log(msg) {
  var p = document.getElementById('log');
  p.innerHTML = msg + "\n" + p.innerHTML;
}

function post() {
  var fd = new FormData();
    
  var submittype = document.getElementById('submittype').subtype.value;
  fd.append('submittype',submittype);

  if(submittype == "text"){
    var comment = document.getElementById('comment').value;
    fd.append('comment', comment);
  }
  else if(submittype == "image" || submittype == "file"){
    const file = document.getElementById("file").files[0];
    fd.append('avatar', file);
  }
  else if(submittype == "freehand"){
    img_url = canvas.toDataURL("image/png").replace(new RegExp("data:image/png;base64,"),"");
    fd.append('comment',img_url);
  }
    
  const param = {
    method: "POST",
    body: fd
  }
  fetch("./index.php", param).then((res) => {
    if (res.ok) {
      window.location.reload();
    }
  });
}
</script>
    </hr>
    This BBS operates with <a href="https://ja.osdn.net/projects/sciencebbs/">ScienceBBS</a>.
  </body>
</html>
