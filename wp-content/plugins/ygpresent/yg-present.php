<?php
/*
Plugin Name: YG Present
Plugin URI: yg-present.com
Version: 1.0.0
Author: Zeter Lee
Description: YG Present
 */

require_once('class/promotion-settings.php' );
require_once('yg-email-subscriber.php');
require_once ('404-redirect.php');


$email_subscribe_cur_version = '1.0';
register_activation_hook( __FILE__, 'email_subscriber_install' );

//Install Email Subscriber Database Table install
function email_subscriber_install() {

  global $wpdb;
  global $email_subscriber_cur_version;
  $dbVersion = get_option('email_subscriber_db_version');

  if($dbVersion == false){
    add_option( 'email_subscriber_db_version', $email_subscriber_cur_version );
  }else if($email_subscriber_cur_version == $dbVersion){

    /** No Need to Install Database */

    return;
  }

  $table_name = $wpdb->prefix . 'email_subscriber';
  $charset_collate = $wpdb->get_charset_collate();


  $sql = "CREATE TABLE $table_name (
		subscriber_id mediumint(9) NOT NULL AUTO_INCREMENT,
		email varchar(255) NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY id (subscriber_id)
	) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}