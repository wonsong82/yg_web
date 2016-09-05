<?php

/**
 * Created by PhpStorm.
 * User: Zeter
 * Date: 9/4/2016
 * Time: 9:49 PM
 */

class EmailSubscriber
{

  private $email_subscriber_table;

  public function __construct()
  {
    global $wpdb;
    $this->email_subscriber_table = $wpdb->prefix . 'email_subscriber';

  }


  public function addEmail($email){
    global $wpdb;
    $email = trim($email);

    //check existence of email in database
    $query = " SELECT * FROM {$this->email_subscriber_table} WHERE email='%s' ";
    $res = $wpdb->get_results(
      $wpdb->prepare($query, array($email))
    );

    if($res){
      return true;
    }

    //if no data found then save
    $query = " INSERT INTO {$this->email_subscriber_table} SET email='%s', time='%s' ";
    $wpdb->query(
      $wpdb->prepare($query, array($email,current_time('mysql')))
    );

    return true;
  }

  public function getAllEmails(){
    global $wpdb;

    $query = "SELECT * FROM {$this->email_subscriber_table} ";
    return $wpdb->get_results($query);

  }

}