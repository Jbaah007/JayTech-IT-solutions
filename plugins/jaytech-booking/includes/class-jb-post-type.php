<?php
/**
 * Appointment custom post type.
 *
 * @package JayTech_Booking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the jt_appointment post type.
 */
class JB_Post_Type {

	/**
	 * Hook into WordPress.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Register the post type. Appointments are created only through the frontend form.
	 */
	public static function register() {
		register_post_type(
			JB_Data::POST_TYPE,
			array(
				'labels'          => array(
					'name'          => __( 'Appointments', 'jaytech-booking' ),
					'singular_name' => __( 'Appointment', 'jaytech-booking' ),
					'edit_item'     => __( 'Appointment Details', 'jaytech-booking' ),
					'all_items'     => __( 'All Appointments', 'jaytech-booking' ),
					'search_items'  => __( 'Search Appointments', 'jaytech-booking' ),
					'not_found'     => __( 'No appointments found.', 'jaytech-booking' ),
					'menu_name'     => __( 'Appointments', 'jaytech-booking' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_icon'       => 'dashicons-calendar-alt',
				'menu_position'   => 26,
				'supports'        => array( 'title' ),
				'capability_type' => array( 'jt_appointment', 'jt_appointments' ),
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
				'has_archive'     => false,
				'rewrite'         => false,
			)
		);
	}

	/**
	 * Grant appointment capabilities to administrators on activation.
	 */
	public static function add_capabilities() {
		$role = get_role( 'administrator' );
		if ( ! $role ) {
			return;
		}

		$caps = array(
			'edit_jt_appointments',
			'edit_others_jt_appointments',
			'edit_private_jt_appointments',
			'edit_published_jt_appointments',
			'publish_jt_appointments',
			'read_private_jt_appointments',
			'delete_jt_appointments',
			'delete_others_jt_appointments',
			'delete_private_jt_appointments',
			'delete_published_jt_appointments',
		);

		foreach ( $caps as $cap ) {
			$role->add_cap( $cap );
		}
	}
}
