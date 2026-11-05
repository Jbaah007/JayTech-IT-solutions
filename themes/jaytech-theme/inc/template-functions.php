<?php
/**
 * Template helpers: content defaults, services, icons, links.
 *
 * @package JayTech_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default text for Customizer-editable content. Contact values are placeholders.
 *
 * @return array
 */
function jaytech_defaults() {
	return array(
		'hero_eyebrow' => __( 'Reliable IT. Real results.', 'jaytech-theme' ),
		'hero_title'   => __( 'Technology solutions that keep your business moving forward.', 'jaytech-theme' ),
		'hero_text'    => __( 'From everyday technical support to cloud, networking, and web development, JayTech IT Solutions is your dedicated technology partner.', 'jaytech-theme' ),
		'intro_title'  => __( 'IT support that speaks your language', 'jaytech-theme' ),
		'intro_text'   => __( 'JayTech IT Solutions helps people and businesses get the most from their technology. We keep things running, plan for growth, and explain everything in plain terms.', 'jaytech-theme' ),
		'footer_text'  => __( 'Technical support, cloud, networking, and web development services.', 'jaytech-theme' ),
		'phone'        => '(000) 000-0000',
		'email'        => 'info@example.com',
		'address'      => __( 'Your business address here', 'jaytech-theme' ),
		'hours'        => __( 'Mon - Fri, 9:00 - 17:00', 'jaytech-theme' ),
	);
}

/**
 * Get a Customizer value with its default.
 *
 * @param string $key Setting key.
 * @return string
 */
function jaytech_mod( $key ) {
	$defaults = jaytech_defaults();
	return get_theme_mod( 'jaytech_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * URL of a page by slug; falls back to the expected permalink.
 *
 * @param string $slug Page slug. Empty for home.
 * @return string
 */
function jaytech_page_url( $slug ) {
	if ( '' === $slug ) {
		return home_url( '/' );
	}

	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * Main site links (label => page slug) used by fallback menus.
 *
 * @return array
 */
function jaytech_primary_links() {
	return array(
		array( __( 'Home', 'jaytech-theme' ), '' ),
		array( __( 'Support Tickets', 'jaytech-theme' ), 'support-tickets' ),
		array( __( 'Customer Dashboard', 'jaytech-theme' ), 'customer-dashboard' ),
		array( __( 'Book Appointment', 'jaytech-theme' ), 'book-appointment' ),
	);
}

/**
 * Print a list of the main links, used when no menu is assigned.
 *
 * @param string $class List CSS class.
 */
function jaytech_fallback_menu( $class = 'primary-menu' ) {
	// wp_nav_menu() passes its args array when used as fallback_cb.
	$class = is_string( $class ) ? $class : 'primary-menu';
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( jaytech_primary_links() as $link ) {
		// Only link to pages that exist and are published.
		if ( '' !== $link[1] && ! jaytech_page_published( $link[1] ) ) {
			continue;
		}
		$current = '' === $link[1] ? is_front_page() : is_page( $link[1] );
		printf(
			'<li><a href="%1$s"%2$s>%3$s</a></li>',
			esc_url( jaytech_page_url( $link[1] ) ),
			$current ? ' aria-current="page"' : '',
			esc_html( $link[0] )
		);
	}
	echo '</ul>';
}

/**
 * Footer fallback menu wrapper.
 */
function jaytech_fallback_footer_menu() {
	jaytech_fallback_menu( 'footer-links' );
}

/**
 * Services shown on the homepage and Services template.
 *
 * @return array
 */
function jaytech_get_services() {
	return array(
		array(
			'icon'   => 'support',
			'title'  => __( 'Technical Support', 'jaytech-theme' ),
			'text'   => __( 'Practical help when technology gets in the way, for individuals and teams.', 'jaytech-theme' ),
			'points' => array(
				__( 'Help desk and troubleshooting', 'jaytech-theme' ),
				__( 'Hardware and software issues', 'jaytech-theme' ),
				__( 'Device and account setup', 'jaytech-theme' ),
				__( 'Ongoing maintenance', 'jaytech-theme' ),
			),
		),
		array(
			'icon'   => 'cloud',
			'title'  => __( 'Cloud Solutions', 'jaytech-theme' ),
			'text'   => __( 'Move to the cloud with a plan that fits how your business works.', 'jaytech-theme' ),
			'points' => array(
				__( 'Cloud migration planning', 'jaytech-theme' ),
				__( 'Backup and recovery setup', 'jaytech-theme' ),
				__( 'Email and collaboration tools', 'jaytech-theme' ),
				__( 'Hosting and storage', 'jaytech-theme' ),
			),
		),
		array(
			'icon'   => 'network',
			'title'  => __( 'Networking', 'jaytech-theme' ),
			'text'   => __( 'Reliable, secure connectivity for offices and home workspaces.', 'jaytech-theme' ),
			'points' => array(
				__( 'Wired and wireless design', 'jaytech-theme' ),
				__( 'Router and firewall configuration', 'jaytech-theme' ),
				__( 'Network troubleshooting', 'jaytech-theme' ),
				__( 'Secure remote access', 'jaytech-theme' ),
			),
		),
		array(
			'icon'   => 'code',
			'title'  => __( 'Web Development', 'jaytech-theme' ),
			'text'   => __( 'Fast, mobile-friendly websites built to represent your business well.', 'jaytech-theme' ),
			'points' => array(
				__( 'Business websites', 'jaytech-theme' ),
				__( 'WordPress themes and plugins', 'jaytech-theme' ),
				__( 'Performance and security basics', 'jaytech-theme' ),
				__( 'Updates and maintenance', 'jaytech-theme' ),
			),
		),
	);
}

/**
 * Inline SVG icon from a fixed set. Output is static markup defined here.
 *
 * @param string $name Icon name.
 * @return string
 */
function jaytech_icon( $name ) {
	$paths = array(
		'support' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
		'cloud'   => '<path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"/>',
		'network' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>',
		'code'    => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
		'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
		'zap'     => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
		'users'   => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
		'mail'    => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
		'phone'   => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>',
		'pin'     => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
		'clock'   => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
		'check'   => '<polyline points="20 6 9 17 4 12"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return '<svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}
