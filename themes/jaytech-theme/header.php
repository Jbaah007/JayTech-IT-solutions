<?php
/**
 * Site header.
 *
 * @package JayTech_Theme
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'jaytech-theme' ); ?></a>

<header class="site-header">
	<div class="container header-inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php bloginfo( 'name' ); ?>
				</a>
			<?php endif; ?>
		</div>

		<button class="menu-toggle" aria-controls="primary-nav" aria-expanded="false">
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'jaytech-theme' ); ?></span>
			<span class="menu-toggle-bar"></span>
		</button>

		<nav id="primary-nav" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary', 'jaytech-theme' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_class'     => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => 'jaytech_fallback_menu',
				)
			);
			?>
			<a class="btn btn-primary nav-cta" href="<?php echo esc_url( jaytech_page_url( 'support-tickets' ) ); ?>"><?php esc_html_e( 'Get Support', 'jaytech-theme' ); ?></a>
		</nav>
	</div>
</header>

<main id="content" class="site-content">
