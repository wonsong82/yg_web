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

  <article class="static-content <?php echo $post->post_name;?>">
  <?php
  $pagestolook = array("my-account","my-orders");
  $home_url = get_home_url();
  if(in_array($post->post_name, $pagestolook) ){
    echo '<div class="menu-bar">
            <div class="menu-title">My Account</div>
            <div class="menu-body">
                <ul>
                    <li><a href="'.$home_url.'/my-account">Overview</a></li>
                    <li><a href="'.$home_url.'/my-account/edit-account">Profile</a></li>
                    <li><a href="'.$home_url.'/my-orders">My Order</a></li>
                    <li><a href="'.$home_url.'/my-account">My Address</a></li>
                    <li><a href="'.$home_url.'/wp-login.php?loggedout=true">log out</a></li>  
                </ul>
            </div>
          </div>';
  }


  the_content();
  ?>
  </article>

</div>


<?php
require __DIR__ . '/inc/footer.php';
