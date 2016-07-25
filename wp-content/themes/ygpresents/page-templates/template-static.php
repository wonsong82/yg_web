<?php
/**
 * Template Name: Static
 *
 */

the_post(); // Parse and get post
$postName = $post->post_name; // Get uname from current post


require __DIR__ . '/inc/header.php';
?>

<article class="static-content <?php echo $post->post_name;?>">
  <?php the_content() ?>
</article>


<?php
require __DIR__ . '/inc/footer.php';
