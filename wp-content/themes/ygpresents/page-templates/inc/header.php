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

<div class="StaticPage">