<?php
/*
Plugin Name: Music Sampler
Plugin URI: http://ygpresents.com
Description: Samples out mp3 files
Author: Won Song
Version: 1.0.0
Author URI: http://wonsong.com
Text Domain: music-sampler
Domain Path:
License:
*/
//require_once __DIR__ . '/mp3/class.mp3.php';

class MusicSampler {

  function __construct(){

    register_activation_hook( __FILE__, [$this, 'activate'] );
    register_deactivation_hook( __FILE__, [$this, 'deactivate'] );
    add_action('save_post', [$this, 'check_sample_musics'] );

  }

  function check_sample_musics() {
    $uploadDir = wp_upload_dir()['basedir'];
    $files = $this->rsearch($uploadDir . '/woocommerce_uploads', '#^.+?\.mp3$#');

    foreach($files as $file){

      $sampleFile = $uploadDir . str_replace('.mp3' ,'-sample.mp3' ,str_replace($uploadDir . '/woocommerce_uploads' , '' , $file));

      if(!file_exists($sampleFile)){
        $this->createSampleFileUsingFFMpeg($file, $sampleFile);
      }
    }
  }

  function createSampleFileUsingFFMpeg($src, $dest){
    $cmd = "ffmpeg -i \"{$src}\" -ss 00:00:00 -acodec libmp3lame -t 00:00:30 \"{$dest}\" -y";
    shell_exec($cmd);
  }

  function createSampleFile($file, $sampleFile){
    if(!is_dir(dirname($sampleFile))){
      mkdir(dirname($sampleFile));
    }

    @copy($file, $sampleFile);

    $mp3 = new mp3;
    $mp3->cut_mp3($$file, $sampleFile, 0, 30, 'second', false);
    exit;

  }


  function rsearch($folder, $pattern) {
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
    $fileList = array();
    foreach($files as $file) {
      $fileList = array_merge($fileList, $file);
    }
    return $fileList;
  }


  function activate(){

  }

  function deactivate(){

  }

  function debug($str){
    file_put_contents(__DIR__ . '/debug.txt', chr(239) . chr(187) . chr(191) . $str."\r", FILE_APPEND);
  }

}



$musicSampler = new MusicSampler();