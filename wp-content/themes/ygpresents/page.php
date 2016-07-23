<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ThemeGrill
 * @subpackage eStore
 * @since eStore 0.1
 */

//get_header(); ?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>YG Presents</title>
    <link rel="stylesheet" type="text/css" href="http://localhost:8080/app.css">
</head>
<body>


	<?php
	do_action( 'estore_before_body_content' );

	$estore_layout = estore_layout_class();
	?>
<div class="StaticPage">
	<div id="content" class="site-content"><!-- #content.site-content -->
		<div class="page-header clearfix">
			<div class="tg-container">
				<?php the_title('<h2 class="entry-title">', '</h2>'); ?>
				<h3 class="entry-sub-title"><?php estore_breadcrumbs(); ?></h3>
			</div>
		</div>
		<main id="main" class="clearfix <?php echo esc_attr($estore_layout); ?>">
			<div class="tg-container">
				<div id="primary">
					<?php
					while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'template-parts/content', 'page' ); ?>

						<?php
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
						
						get_template_part('navigation', 'none');
						
					endwhile; // End of the loop. ?>
				</div> <!-- Primary end -->
					<?php estore_sidebar_select(); ?>
			</div>
		</main>
	</div>

	<?php do_action( 'estore_after_body_content' ); ?>
</div>

<div id="root">
    <div class="page-loading">
        <div class="page-loading__spinner">
            <div class="Spinner">
                <span class="tl box"></span>
                <span class="tr box"></span>
                <span class="bl box"></span>
                <span class="br box"></span>
            </div>
        </div>
    </div>
</div>


<script src="http://localhost:8080/app.js"></script>
</body>
</html>





<?php //get_footer(); ?>



