<?php
$appCss = (defined('WP_DEBUG') && WP_DEBUG) ?
  'http://localhost:8080/app.css' :
  '/static/app.css';

$staticCss = file_exists(ABSPATH . 'static/static-page.css') ?
  '<link rel="stylesheet" type="text/css" href="/static/static-page.css">' : '';

$staticPageCss = file_exists(ABSPATH . "static/static-{$postName}.css") ?
  '<link rel="stylesheet" type="text/css" href="/static/static-' . $postName . '.css">' : '';

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
  <?php echo $staticCss ?>
  <?php echo $staticPageCss ?>

</head>
<body>

<div class="StaticPage">