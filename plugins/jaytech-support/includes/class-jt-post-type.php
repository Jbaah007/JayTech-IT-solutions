<?php
/**
 * Ticket custom post type.
 *
 * @package JayTech_Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the jt_ticket post type.
 */
class JT_Post_Type {

	/**
	 * Hook into WordPress.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Register the post type. Not public: only admins manage tickets in wp-admin,
	 * customers see their own through the shortcode.
	 */
	public static function register() {
		register_post_type(
			JT_Data::POST_TYPE,
			array(
				'labels'          => array(
					'name'          => __( 'Support Tickets', 'jaytech-support' ),
					'singular_name' => __( 'Support Ticket', 'jaytech-support' ),
					'add_new_item'  => __( 'Add New Ticket', 'jaytech-support' ),
					'edit_item'     => __( 'Edit Ticket', 'jaytech-support' ),
					'all_items'     => __( 'All Tickets', 'jaytech-support' ),
					'search_items'  => __( 'Search Tickets', 'jaytech-support' ),
					'not_found'     => __( 'No tickets found.', 'jaytech-support' ),
					'menu_name'     => __( 'Tickets', 'jaytech-support' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_icon'       => 'dashicons-tickets-alt',
				'menu_position'   => 25,
				'supports'        => array( 'title', 'editor', 'author' ),
				'capability_type' => array( 'jt_ticket', 'jt_tickets' ),
				'map_meta_cap'    => true,
				'has_archive'     => false,
				'rewrite'         => false,
			)
		);
	}

	/**
	 * Grant ticket capabilities to administrators on activation.
	 */
	public static function add_capabilities() {
		$role = get_role( 'administrator' );
		if ( ! $role ) {
			return;
		}

		$caps = array(
			'edit_jt_tickets',
			'edit_others_jt_tickets',
			'edit_private_jt_tickets',
			'edit_published_jt_tickets',
			'publish_jt_tickets',
			'read_private_jt_tickets',
			'delete_jt_tickets',
			'delete_others_jt_tickets',
			'delete_private_jt_tickets',
			'delete_published_jt_tickets',
		);

		foreach ( $caps as $cap ) {
			$role->add_cap( $cap );
		}
	}
}
