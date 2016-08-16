<?php

$appCss = (defined('WP_DEBUG') && WP_DEBUG) ?
  'http://localhost:8080/app.css' :
  '/static/app.css';

$appJS = (defined('WP_DEBUG') && WP_DEBUG) ?
  'http://localhost:8080/app.js' :
  '/static/app.js';


?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>YG Presents</title>

  <link rel="stylesheet" type="text/css" href="<?php echo $appCss?>">

</head>
<body>

<div id="root">
  <div class="page-loading">
    <div class="page-loading__spinner">
      <div class="Spinner">
        <span class="tl box"></span>
        <span class="tr box"></span>
        <span class="bl box"></span>
        <span class="br box"></span>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $appJS ?>"></script>


</body>
</html>
