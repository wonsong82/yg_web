<?php
/*
Plugin Name: WooCommerce Order Export and More
Plugin URI: http://www.jem-products.com
Description: Export your woocommerce orders and more with this free plugin
Version: 1.2.4
Author: JEM Plugins
Author URI: http://www.jem-products.com
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define ( 'JEM_EXP_PLUGIN_PATH' , plugin_dir_path( __FILE__ ) );
define('JEM_EXP_DOMAIN', 'jem-woocommerce-exporter');
define( 'JEM_EXP_URL', plugin_dir_url( __FILE__ ) );

//only proceed if we are in admin mode!
if( ! is_admin() ){
	return;
}

//Globals
global $jem_export_globals;


$entities = array();
$entities[] = "Product";
$entities[] = "Order";
$entities[] = "Customer";
$entities[] = "Shipping";
$entities[] = "Coupon";
$entities[] = "Categories";
$entities[] = "Tags";

//Create an array of which entities are active
$active = array();
$active["Product"] = true;
$active["Order"] = true;

$jem_export_globals['entities'] = $entities;
$jem_export_globals['active'] = $active;

//Include the basic stuff
include_once(JEM_EXP_PLUGIN_PATH . 'inc/jem-exporter.php');
include_once(JEM_EXP_PLUGIN_PATH . 'inc/BaseEntity.php');

//include the entities
foreach($jem_export_globals['entities'] as $entity){
	include_once(JEM_EXP_PLUGIN_PATH . 'inc/' . $entity . '.php');

}

/**
 * Loads the right js & css assets
*/
function load_jem_exp_scripts(){



	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	//wp_enqueue_script('jquery-ui-tabs');
	
 	//Need the jquery CSS files
	global $wp_scripts;
	$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
	// Admin styles for WC pages only
	wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
	wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );
	
	
	wp_enqueue_style('dashicons');
		
	wp_enqueue_script( 'jem-css',  plugin_dir_url( __FILE__ ). 'js/main.js' );
	wp_enqueue_style( 'jem-css',  plugin_dir_url( __FILE__ ). 'css/jem-export-lite.css' );
}


add_action('admin_enqueue_scripts', 'load_jem_exp_scripts');

$jem_exporter_lite = new JEM_export_lite();

?>