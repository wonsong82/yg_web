<?php
$appCss = (defined('WP_DEBUG') && WP_DEBUG) ?
	'http://localhost:8080/app.css' :
	'/static/app.css';

$staticCss = file_exists(ABSPATH . 'static/static-page.css') ?
	'<link rel="stylesheet" type="text/css" href="/static/static-page.css">' : '';

$postName = 'checkout'; // Get uname from current post

$staticPageCss = file_exists(ABSPATH . "static/static-{$postName}.css") ?
	'<link rel="stylesheet" type="text/css" href="/static/static-' . $postName . '.css">' : '';

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>YG Presents</title>

	<link rel="stylesheet" type="text/css" href="<?php echo $appCss?>">

	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/manifest.json">
	<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="apple-mobile-web-app-title" content="YG Presents">
	<meta name="application-name" content="YG Presents">
	<meta name="theme-color" content="#ffffff">

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
	<?php echo $staticCss ?>
	<?php echo $staticPageCss ?>
</head>

<body >
<div class="StaticPage">
	<?php do_action( 'tg_before' ); ?>
<!--	<div id="page" class="hfeed site">-->
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'estore' ); ?></a>

		<?php do_action( 'estore_before_header' ); ?>
		<header id="masthead" class="site-header" role="banner" style="display:none;">
		<?php if( get_theme_mod( 'estore_bar_activation' ) == '1' ) : ?>
			<div class="top-header-wrapper clearfix">
				<div class="tg-container" style="display:none;">
					<div class="left-top-header">
						<div id="header-ticker" class="left-header-block">
							<?php
							$header_bar_text = get_theme_mod( 'estore_bar_text' );
							echo wp_kses_post($header_bar_text);
							?>
						</div> <!-- header-ticker end-->
					</div> <!-- left-top-header end -->

					<div class="right-top-header">
						<div class="top-header-menu-wrapper">
							<?php wp_nav_menu(
								array(
									'theme_location' => 'header',
									'menu_id'        => 'header-menu',
									'fallback_cb'    => false
								)
							);
							?>
						</div> <!-- top-header-menu-wrapper end -->
						<?php
						if (class_exists('woocommerce')):
						if(get_theme_mod('estore_header_ac_btn', '' ) == '1' ):
						?>
						<div class="login-register-wrap right-header-block">
							<?php if ( is_user_logged_in() ) { ?>
									<a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') )); ?>" title="<?php esc_attr__('My Account','estore'); ?>" class="user-icon"><?php esc_html_e('My Account', 'estore'); ?></a>
								<?php }
								else { ?>
									<a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') )); ?>" title="<?php esc_attr__('Login/Register','estore'); ?>"class="user-icon"><?php esc_html_e('Login/ Register', 'estore'); ?><i class="fa fa-angle-down"> </i></a>
								<?php } ?>
						</div>
						<?php endif;
						if(get_theme_mod('estore_header_currency', '' ) == '1' ):
						?>
						<div class="currency-wrap right-header-block">
							<a href="#"><?php echo esc_html( get_woocommerce_currency()); ?><?php echo "(" . esc_html ( get_woocommerce_currency_symbol() ) . ")"; ?></a>
						</div> <!--currency-wrap end -->
						<?php endif; // header currency check ?>

						<?php
						if (function_exists('icl_object_id')) {
							if(get_theme_mod( 'estore_header_lang' ) == 1 ) {
								do_action('wpml_add_language_selector');
							}
						}
						endif; // woocommerce check
						?>
					</div>
				</div>
		  </div>
	  	<?php endif; ?>

		 <div class="middle-header-wrapper clearfix">
			<div class="tg-container">
			   <div class="logo-wrapper clearfix">
				 <?php if( ( get_theme_mod( 'estore_logo_placement', 'header_text_only' ) == 'show_both' || get_theme_mod( 'estore_logo_placement', 'header_text_only' ) == 'header_logo_only' ) ) {

				 	// Checking for theme defined logo
				 	if( get_theme_mod( 'estore_logo', '' ) != '' ) {
				 	?>
					<div class="logo">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo esc_url(get_theme_mod('estore_logo' )); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" /></a>
					</div> <!-- logo end -->
					<?php }
					if( function_exists( 'the_custom_logo' ) && has_custom_logo( $blog_id = 0 ) ) {
						estore_the_custom_logo();
					}

				} // Checks for logo appearance

				$screen_reader = 'with-logo-text';
				if( get_theme_mod( 'estore_logo_placement', 'header_text_only' ) == 'header_logo_only' || get_theme_mod( 'estore_logo_placement', 'header_text_only' ) == 'disable' ) {
					$screen_reader = 'screen-reader-text';
				}
				?>

				<div class="site-title-wrapper <?php echo $screen_reader; ?>">
				<?php if ( is_front_page() || is_home() ) : ?>
					<h1 id="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
					</h1>
				<?php else : ?>
					<h3 id="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
					</h3>
				<?php endif;
				$description = get_bloginfo( 'description', 'display' );
				if ( $description || is_customize_preview() ) : ?>
					<p id="site-description"><?php echo $description; ?></p>
				<?php endif; ?>
				  </div>
			   </div><!-- logo-end-->

			<div class="wishlist-cart-wrapper clearfix">
				<?php
				if (function_exists('YITH_WCWL')) {
					$wishlist_url = YITH_WCWL()->get_wishlist_url();
					?>
					<div class="wishlist-wrapper">
						<a class="quick-wishlist" href="<?php echo esc_url($wishlist_url); ?>" title="Wishlist">
							<i class="fa fa-heart"></i>
							<span class="wishlist-value"><?php echo absint( yith_wcwl_count_products() ); ?></span>
						</a>
					</div>
					<?php
				}
				if ( class_exists( 'woocommerce' ) ) : ?>
					<div class="cart-wrapper">
						<div class="estore-cart-views">
							<a href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" class="wcmenucart-contents">
								<i class="fa fa-shopping-cart"></i>
								<span class="cart-value"><?php echo wp_kses_data ( WC()->cart->get_cart_contents_count() ); ?></span>
							</a> <!-- quick wishlist end -->
							<div class="my-cart-wrap">
								<div class="my-cart"><?php esc_html_e('Total', 'estore'); ?></div>
								<div class="cart-total"><?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?></div>
							</div>
						</div>
						<?php the_widget( 'WC_Widget_Cart', '' ); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php get_sidebar( 'header' ); ?>

			</div>
		 </div> <!-- middle-header-wrapper end -->

		 <div class="bottom-header-wrapper clearfix">
			<div class="tg-container">

				<?php
				$menu_location  = 'secondary';
				$menu_locations = get_nav_menu_locations();
				$menu_object    = (isset($menu_locations[$menu_location]) ? wp_get_nav_menu_object($menu_locations[$menu_location]) : null);
				$menu_name      = (isset($menu_object->name) ? $menu_object->name : '');
				if ( has_nav_menu( $menu_location ) ) {
				?>
				<div class="category-menu">
					<div class="category-toggle">
						<?php echo esc_html($menu_name); ?><i class="fa fa-navicon"> </i>
					</div>
					<nav id="category-navigation" class="category-menu-wrapper hide" role="navigation">
						<?php wp_nav_menu(
							array(
								'theme_location' => 'secondary',
								'menu_id'        => 'category-menu',
								'fallback_cb'    => 'false'
							)
						);
						?>
					</nav>
				</div>
				<?php } ?>

 				<div class="search-user-wrapper clearfix">
					<div class="search-wrapper search-user-block">
						<div class="search-icon">
							<i class="fa fa-search"> </i>
						</div>
						<div class="header-search-box">
							<?php get_search_form(); ?>
						</div>
					</div>
					<div class="user-wrapper search-user-block">
						<?php if ( is_user_logged_in() ) { ?>
							<a href="<?php echo esc_url (get_permalink( get_option('woocommerce_myaccount_page_id') )); ?>" title="<?php esc_attr__('My Account','estore'); ?>" class="user-icon"><i class="fa fa-user"></i></a>
						<?php }
						else { ?>
							<a href="<?php echo esc_url(get_permalink( get_option('woocommerce_myaccount_page_id') )); ?>" title="<?php esc_attr__('Login / Register','estore'); ?>" class="user-icon"><i class="fa fa-user-times"></i></a>
						<?php } ?>
					</div>
				</div> <!-- search-user-wrapper -->
				<nav id="site-navigation" class="main-navigation" role="navigation">
				<div class="toggle-wrap"><span class="toggle"><i class="fa fa-reorder"> </i></span></div>
					<?php wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_id'        => 'primary-menu',
						)
					);
					?>
			   </nav><!-- #site-navigation -->

			</div>
		 </div> <!-- bottom-header.wrapper end -->
	</header>
	<?php do_action( 'estore_after_header' ); ?>
	<?php do_action( 'estore_before_main' ); ?>
