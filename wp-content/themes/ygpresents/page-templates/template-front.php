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

  <!--
  Favicons:
     for iOS - Add to homescreen for iPhone and iPad
     for Android Chrome - Add to Homescreen for android
     for Windows 8 & 10 - Can pin site on desktop
     for Sarafi - Pinned Tab
  -->
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
  <link rel="manifest" href="/manifest.json">
  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="apple-mobile-web-app-title" content="YG Presents">
  <meta name="application-name" content="YG Presents">
  <meta name="theme-color" content="#ffffff">
  <!-- End Favicons -->

</head>
<body>

<div id="root">
  <div class="page-loading">
    <div class="page-loading__spinner">
      <div class="SquareSpinner">
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
