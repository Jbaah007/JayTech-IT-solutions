<?php
/**
 * Shared ticket option lists.
 *
 * @package JayTech_Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed values for ticket fields.
 */
class JT_Data {

	const POST_TYPE = 'jt_ticket';

	/**
	 * Ticket statuses (admin-controlled).
	 *
	 * @return array
	 */
	public static function statuses() {
		return array(
			'open'        => __( 'Open', 'jaytech-support' ),
			'in_progress' => __( 'In Progress', 'jaytech-support' ),
			'resolved'    => __( 'Resolved', 'jaytech-support' ),
		);
	}

	/**
	 * Ticket categories.
	 *
	 * @return array
	 */
	public static function categories() {
		return array(
			'technical_support' => __( 'Technical Support', 'jaytech-support' ),
			'cloud'             => __( 'Cloud Solutions', 'jaytech-support' ),
			'networking'        => __( 'Networking', 'jaytech-support' ),
			'web_development'   => __( 'Web Development', 'jaytech-support' ),
			'other'             => __( 'Other', 'jaytech-support' ),
		);
	}

	/**
	 * Ticket priorities.
	 *
	 * @return array
	 */
	public static function priorities() {
		return array(
			'low'    => __( 'Low', 'jaytech-support' ),
			'medium' => __( 'Medium', 'jaytech-support' ),
			'high'   => __( 'High', 'jaytech-support' ),
			'urgent' => __( 'Urgent', 'jaytech-support' ),
		);
	}

	/**
	 * Get a ticket field value, falling back to a default key.
	 *
	 * @param int    $post_id Ticket ID.
	 * @param string $field   status|category|priority.
	 * @return string
	 */
	public static function get_field( $post_id, $field ) {
		$defaults = array(
			'status'   => 'open',
			'category' => 'other',
			'priority' => 'medium',
		);
		$lists    = array(
			'status'   => self::statuses(),
			'category' => self::categories(),
			'priority' => self::priorities(),
		);
		$value    = get_post_meta( $post_id, '_jt_' . $field, true );

		return isset( $lists[ $field ][ $value ] ) ? $value : $defaults[ $field ];
	}
}
