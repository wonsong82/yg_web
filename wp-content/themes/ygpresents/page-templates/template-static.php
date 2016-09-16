<?php
/**
 * Template Name: Static
 *
 */

the_post(); // Parse and get post
$postName = $post->post_name; // Get uname from current post


require __DIR__ . '/inc/header.php';
?>

<div class="StaticPage">

  <article class="static-content <?php echo $postName;?>">
  <?php
  $pagestolook = array("my-account","my-orders","my-downloads");
  $home_url = get_home_url();
  if(in_array($postName, $pagestolook) ){
    $yg_cur = ygGetCurrentPage();
    ?>

    <div class="menu-bar">
      <div class="menu-title">My Account</div>
      <div class="menu-body">
          <ul>
              <li><a href="<?php echo $home_url?>/my-account"<?php echo $yg_cur=='dashboard'?' class="active"':''?>>Dashboard</a></li>
              <li><a href="<?php echo $home_url?>/my-account/edit-account"<?php echo $yg_cur=='profile'?' class="active"':''?>>Edit Profile</a></li>
              <li><a href="<?php echo $home_url?>/my-account/edit-address"<?php echo $yg_cur=='address'?' class="active"':''?>>My Address</a></li>
              <li><a href="<?php echo $home_url?>/my-orders"<?php echo $yg_cur=='order'?' class="active"':''?>>My Orders</a></li>
              <li><a href="<?php echo $home_url?>/my-downloads"<?php echo $yg_cur=='download'?' class="active"':''?>>My Downloads</a></li>
              <li><a href="<?php echo $home_url?>/customer-logout">Log Out</a></li>
          </ul>
      </div>
    </div>

    <?php
  }


  the_content();
  ?>
  </article>

</div>



<?php
function ygGetCurrentPage(){
  $uri = rtrim($_SERVER['REQUEST_URI'], '/');
  if(preg_match('#/my-account$#', $uri)){
    return 'dashboard';
  }
  else if(preg_match('#/edit-account$#', $uri)){
    return 'profile';
  }
  else if(preg_match('#/my-orders$#', $uri)){
    return 'order';
  }
  else if(preg_match('#/my-downloads#', $uri)){
    return 'download';
  }
  else if(preg_match('#/my-account/view-order/#', $uri)){
    return 'order';
  }
  else if(preg_match('#/edit-address#', $uri)){
    return 'address';
  }
  else {
    return '';
  }
}


require __DIR__ . '/inc/footer.php';
