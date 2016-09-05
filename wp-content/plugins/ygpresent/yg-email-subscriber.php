<?php
/**
 * Created by PhpStorm.
 * User: Zeter
 * Date: 9/4/2016
 * Time: 10:44 PM
 */

add_action('admin_menu', 'email_sub_add_menu');
require_once ('class/EmailSubscriber.php');

function email_sub_add_menu(){
  add_menu_page('Email Subscriber', 'Email Subscriber', 'edit_users', 'email-subscriber_list', 'get_email_lists');
}

function get_email_lists(){
  $email_subscriber = new EmailSubscriber();
  $lists = $email_subscriber->getAllEmails();

  echo "<h1>" . 'EMAIL SUBSCRIBER LIST' . "</h1>";
  echo "<br>";
  ?>

  <table class="table-style-two" width="50%">
    <tr>
      <th>NO</th>
      <th>EMAIL</th>
      <th>DATE OF SIGNUP</th>
    </tr>
    <?php
    foreach ($lists as $list){?>

      <tr>
        <td><?=$list->subscriber_id?></td>
        <td><?=$list->email?></td>
        <td><?=$list->time?></td>
      </tr>
    <?php
    }?>
  </table>
<?php
}

