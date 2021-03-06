<?php
/**
/**
 * Template Name: Checkout
 *
 */
//the_post(); // Parse and get post
$postName = 'checkout'; // Get uname from current post
get_header('checkout'); ?>
<?php
do_action( 'estore_before_body_content' );

$estore_layout = estore_layout_class();
?>
<article class="static-content <?php echo $post->post_name;?>">
  <div class="static-content-wrapper">
    <div id="content" class="site-content" ><!-- #content.site-content -->
      <div class="page-header clearfix" style="">
        <div class="tg-container">
          <?php the_title('<h2 class="static-content-title">', '</h2>'); ?>
          <h3 class="entry-sub-title"><?php estore_breadcrumbs(); ?></h3>
        </div>
      </div>
      <div id="main" class="clearfix <?php echo esc_attr($estore_layout); ?>">
        <div class="tg-container" >
            <?php
            while ( have_posts() ) : the_post(); ?>

              <?php get_template_part( 'template-parts/content', 'page' ); ?>

              <?php
              // If comments are open or we have at least one comment, load up the comment template.
              if ( comments_open() || get_comments_number() ) :
                comments_template();
              endif;

    //          get_template_part('navigation', 'none');

            endwhile; // End of the loop. ?>
        </div>
      </div>
    </div>
  </div>

</article>

<?php do_action( 'estore_after_body_content' ); ?>

<?php get_footer('checkout'); ?>
<!---->
<?php
//require __DIR__ . '/inc/footer.php';