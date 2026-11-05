<?php
/**
 * Shared booking data and settings.
 *
 * @package JayTech_Booking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option lists, settings getters, and meta helpers.
 */
class JB_Data {

	const POST_TYPE = 'jt_appointment';

	/**
	 * Bookable services.
	 *
	 * @return array
	 */
	public static function services() {
		return array(
			'technical_support' => __( 'Technical Support', 'jaytech-booking' ),
			'cloud_solutions'   => __( 'Cloud Solutions', 'jaytech-booking' ),
			'networking'        => __( 'Networking', 'jaytech-booking' ),
			'web_development'   => __( 'Web Development', 'jaytech-booking' ),
		);
	}

	/**
	 * Appointment statuses (admin-controlled).
	 *
	 * @return array
	 */
	public static function statuses() {
		return array(
			'pending'   => __( 'Pending', 'jaytech-booking' ),
			'confirmed' => __( 'Confirmed', 'jaytech-booking' ),
			'completed' => __( 'Completed', 'jaytech-booking' ),
			'cancelled' => __( 'Cancelled', 'jaytech-booking' ),
		);
	}

	/**
	 * Appointment length in minutes.
	 *
	 * @return int
	 */
	public static function duration() {
		$value = (int) get_option( 'jb_duration', 60 );
		return ( $value >= 15 && $value <= 480 ) ? $value : 60;
	}

	/**
	 * Opening hour (0-23, site time).
	 *
	 * @return int
	 */
	public static function open_hour() {
		$open  = (int) get_option( 'jb_open_hour', 9 );
		$close = (int) get_option( 'jb_close_hour', 17 );
		return ( $open >= 0 && $open <= 23 && $open < $close ) ? $open : 9;
	}

	/**
	 * Closing hour (1-24, site time).
	 *
	 * @return int
	 */
	public static function close_hour() {
		$open  = (int) get_option( 'jb_open_hour', 9 );
		$close = (int) get_option( 'jb_close_hour', 17 );
		return ( $close >= 1 && $close <= 24 && $open < $close ) ? $close : 17;
	}

	/**
	 * Get a validated appointment field.
	 *
	 * @param int    $post_id Appointment ID.
	 * @param string $field   status|service.
	 * @return string
	 */
	public static function get_field( $post_id, $field ) {
		$lists    = array(
			'status'  => self::statuses(),
			'service' => self::services(),
		);
		$defaults = array(
			'status'  => 'pending',
			'service' => 'technical_support',
		);
		$value    = get_post_meta( $post_id, '_jb_' . $field, true );

		return isset( $lists[ $field ][ $value ] ) ? $value : $defaults[ $field ];
	}

	/**
	 * Format a UTC timestamp in the site timezone.
	 *
	 * @param int $timestamp Unix timestamp.
	 * @return string
	 */
	public static function format_time( $timestamp ) {
		return wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $timestamp );
	}
}
