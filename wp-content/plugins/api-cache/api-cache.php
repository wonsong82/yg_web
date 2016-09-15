<?php
/*
Plugin Name: YG API Cache
Plugin URI: http://ygpresents.com
Description: Generate caches when saving
Author: ME
Version: 1.0.0
Author URI: http://wonsong.com
Text Domain: yg-api-cache
Domain Path:
License:
*/

class YGAPICache {

  function __construct(){
    register_activation_hook( __FILE__, [$this, 'activate'] );
    register_deactivation_hook( __FILE__, [$this, 'deactivate'] );
    add_action('save_post', [$this, 'generateCache'] );
  }

  function generateCache(){
    global $post;
    $siteUrl = get_site_url();
    $ch = curl_init();
    if(in_array($post->post_type, ['artist', 'event', 'tour', 'album', 'blog', 'product'])) {
      curl_setopt($ch, CURLOPT_URL, $siteUrl . '/api/generateCache?' . 'type=' . $post->post_type);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_exec($ch);
    }
  }

  function activate(){}
  function deactivate(){}
  function debug($str){
    file_put_contents(__DIR__ . '/debug.txt', chr(239) . chr(187) . chr(191) . $str."\r", FILE_APPEND);
  }

}

$ygApiCache = new YGAPICache();