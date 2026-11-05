<?php
/**
 * Admin notice listing required pages that are missing or unpublished.
 * Pages are never created automatically.
 *
 * @package JayTech_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pages the theme links to: slug => array( title, instruction ).
 *
 * @return array
 */
function jaytech_required_pages() {
	return array(
		'support-tickets'    => array( __( 'Support Tickets', 'jaytech-theme' ), '[jaytech_support_form]' ),
		'customer-dashboard' => array( __( 'Customer Dashboard', 'jaytech-theme' ), '[jaytech_customer_dashboard]' ),
		'book-appointment'   => array( __( 'Book Appointment', 'jaytech-theme' ), '[jaytech_booking_form]' ),
	);
}

/**
 * Whether a published page exists for a slug.
 *
 * @param string $slug Page slug.
 * @return bool
 */
function jaytech_page_published( $slug ) {
	$page = get_page_by_path( $slug );
	return $page && 'publish' === $page->post_status;
}

/**
 * Show the notice to editors.
 */
function jaytech_missing_pages_notice() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	$missing = array();
	foreach ( jaytech_required_pages() as $slug => $info ) {
		if ( ! jaytech_page_published( $slug ) ) {
			$missing[ $slug ] = $info;
		}
	}

	if ( ! $missing ) {
		return;
	}

	echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'JayTech theme: these pages are missing or not published:', 'jaytech-theme' ) . '</strong></p><ul style="list-style:disc;margin-left:1.5em">';
	foreach ( $missing as $slug => $info ) {
		printf(
			'<li>%1$s &mdash; %2$s <code>%3$s</code> &mdash; %4$s</li>',
			esc_html( $info[0] ),
			esc_html__( 'slug', 'jaytech-theme' ),
			esc_html( $slug ),
			esc_html( $info[1] )
		);
	}
	echo '</ul><p><a href="' . esc_url( admin_url( 'post-new.php?post_type=page' ) ) . '">' . esc_html__( 'Add a page', 'jaytech-theme' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'jaytech_missing_pages_notice' );
