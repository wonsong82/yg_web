<?php
/**
 * Created by PhpStorm.
 * User: Zeter
 * Date: 9/4/2016
 * Time: 10:44 PM
 */


add_action('admin_init', 'email_sub_init');
add_action('admin_menu', 'email_sub_add_menu');
require_once ('class/EmailSubscriber.php');

function email_sub_add_menu(){
  add_menu_page('Email Subscriber', 'Email Subscriber', 'edit_users', 'email-subscriber_list', 'get_email_lists');
  add_submenu_page('email-subscriber_list', 'Download List', 'Download List', 'edit_users', 'email-subscriber_download', 'download_email_lists');

}


function email_sub_init(){
  if(strstr($_SERVER['REQUEST_URI'], 'email-subscriber_download')) {

    $email_subscriber = new EmailSubscriber();
    $lists = $email_subscriber->getAllEmails();
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=email_subscriber_list.csv');
    header('Pragma: no-cache');
    echo 'subscriber id' . "," . 'email address' . "," . 'subscribed at' . "\n";
    foreach($lists as $list){
      echo $list->subscriber_id . "," . $list->email . "," . $list->time . "\n";
    }
    exit;
  }
}

function download_list(){

}

function download_email_lists(){

}

function get_email_lists(){
  $email_subscriber = new EmailSubscriber();
  $lists = $email_subscriber->getAllEmails();
  ?>

  <h1>EMAIL SUBSCRIBER LIST</h1><br>
  <div style="width: 50%">
    <table class="widefat fixed" cellpadding="10">
      <thead>
      <tr>
        <th width="20%" class="manage-column column-thumb"><span style="font-weight: bold">NO</span></th>
        <th width="50%" class="manage-column column-columnname" scope="col"><span style="font-weight: bold">EMAIL ADDRESS</span></th>
        <th width="30%" class="manage-column column-thumb"><span style="font-weight: bold">DATE OF SIGNUP</span></th>
      </tr>
      </thead>

      <tbody>
        <?php foreach($lists as $key => $list){
          $trClass = $key % 2 == 0 ? 'alternate' : '';
        ?>
        <tr class="<?=$trClass?>">
          <td><?=$list->subscriber_id?></td>
          <td><?=$list->email?></td>
          <td><?=$list->time?></td>
        </tr>
      </tbody>
      <?php
      }?>
    </table>
  </div>
<?php
}

