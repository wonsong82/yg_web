<?php
/*
Plugin Name: Custom Router
Plugin URI: http://ygpresents.com
Description: Custom router for link to custom template. View and edit the routes.php inside the plugin folder. Works well with SPA.
Author: Won Song
Version: 1.0.0
Author URI: http://wonsong.com
Text Domain: custom-router
Domain Path:
License:
*/
class Route {

  private $routes = [];


  function __construct(){

    $routes = require(__DIR__ . '/routes.php');
    $this->routes = $routes;
    register_activation_hook( __FILE__, [$this, 'activate'] );
    register_deactivation_hook( __FILE__, [$this, 'deactivate'] );
    add_action( 'generate_rewrite_rules', array($this, 'add_rewrite_rules') );
    add_filter( 'query_vars', array($this, 'query_vars') );
  }


  function activate(){

    global $wp_rewrite; $wp_rewrite->flush_rules();
  }


  function deactivate(){

    remove_action( 'generate_rewrite_rules', array($this, 'add_rewrite_rules') );
    global $wp_rewrite; $wp_rewrite->flush_rules();
  }


  function add_rewrite_rules($wp_rewrite){

    $rules = [];
    foreach($this->routes as $route){
      $routeTo = "index.php?page_id={$route['page_post_id']}";
      $i = 1;
      foreach($route['match_params'] as $query_var){
        $routeTo .= "&{$query_var}=\$matches[{$i}]";
        $i++;
      }
      $rules[$route['uri_pattern']] = $routeTo;
    }

    $wp_rewrite->rules = $rules + (array)$wp_rewrite->rules;
  }


  function query_vars($public_query_vars){

    $vars = [];
    foreach($this->routes as $route){
      $vars = array_merge($vars, $route['match_params']);
    }
    $vars = array_unique($vars);

    foreach($vars as $v)
        array_push($public_query_vars, $v);

    return $public_query_vars;
  }


}

$route = new Route();