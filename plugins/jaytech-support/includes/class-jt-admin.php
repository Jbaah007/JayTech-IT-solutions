<?php
/**
 * Admin interface: meta box, list columns, status filter.
 *
 * @package JayTech_Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin-side ticket management.
 */
class JT_Admin {

	/**
	 * Hook into WordPress.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post_' . JT_Data::POST_TYPE, array( __CLASS__, 'save_meta' ) );
		add_filter( 'manage_' . JT_Data::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . JT_Data::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( __CLASS__, 'status_filter' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'apply_status_filter' ) );
	}

	/**
	 * Register the details meta box.
	 */
	public static function add_meta_box() {
		add_meta_box(
			'jt_ticket_details',
			__( 'Ticket Details', 'jaytech-support' ),
			array( __CLASS__, 'render_meta_box' ),
			JT_Data::POST_TYPE,
			'side',
			'high'
		);
	}

	/**
	 * Render the details meta box.
	 *
	 * @param WP_Post $post Ticket.
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'jt_save_ticket_' . $post->ID, 'jt_ticket_nonce' );

		$fields = array(
			'status'   => array( __( 'Status', 'jaytech-support' ), JT_Data::statuses() ),
			'category' => array( __( 'Category', 'jaytech-support' ), JT_Data::categories() ),
			'priority' => array( __( 'Priority', 'jaytech-support' ), JT_Data::priorities() ),
		);

		foreach ( $fields as $key => $field ) {
			$current = JT_Data::get_field( $post->ID, $key );
			printf( '<p><label for="jt_%1$s"><strong>%2$s</strong></label><br>', esc_attr( $key ), esc_html( $field[0] ) );
			printf( '<select name="jt_%1$s" id="jt_%1$s" class="widefat">', esc_attr( $key ) );
			foreach ( $field[1] as $value => $label ) {
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					selected( $current, $value, false ),
					esc_html( $label )
				);
			}
			echo '</select></p>';
		}
	}

	/**
	 * Save meta box values.
	 *
	 * @param int $post_id Ticket ID.
	 */
	public static function save_meta( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['jt_ticket_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jt_ticket_nonce'] ) ), 'jt_save_ticket_' . $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$lists = array(
			'status'   => JT_Data::statuses(),
			'category' => JT_Data::categories(),
			'priority' => JT_Data::priorities(),
		);

		foreach ( $lists as $key => $list ) {
			if ( ! isset( $_POST[ 'jt_' . $key ] ) ) {
				continue;
			}
			$value = sanitize_key( wp_unslash( $_POST[ 'jt_' . $key ] ) );
			if ( isset( $list[ $value ] ) ) {
				update_post_meta( $post_id, '_jt_' . $key, $value );
			}
		}
	}

	/**
	 * List table columns.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public static function columns( $columns ) {
		return array(
			'cb'          => $columns['cb'],
			'title'       => __( 'Subject', 'jaytech-support' ),
			'jt_customer' => __( 'Customer', 'jaytech-support' ),
			'jt_category' => __( 'Category', 'jaytech-support' ),
			'jt_priority' => __( 'Priority', 'jaytech-support' ),
			'jt_status'   => __( 'Status', 'jaytech-support' ),
			'date'        => $columns['date'],
		);
	}

	/**
	 * Render list table column.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Ticket ID.
	 */
	public static function column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'jt_customer':
				echo esc_html( get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ) );
				break;
			case 'jt_category':
				$list = JT_Data::categories();
				echo esc_html( $list[ JT_Data::get_field( $post_id, 'category' ) ] );
				break;
			case 'jt_priority':
				$list = JT_Data::priorities();
				echo esc_html( $list[ JT_Data::get_field( $post_id, 'priority' ) ] );
				break;
			case 'jt_status':
				$list = JT_Data::statuses();
				echo esc_html( $list[ JT_Data::get_field( $post_id, 'status' ) ] );
				break;
		}
	}

	/**
	 * Status dropdown above the list table.
	 *
	 * @param string $post_type Current post type.
	 */
	public static function status_filter( $post_type ) {
		if ( JT_Data::POST_TYPE !== $post_type ) {
			return;
		}

		// Read-only filter on a list view; no state change.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current = isset( $_GET['jt_status'] ) ? sanitize_key( wp_unslash( $_GET['jt_status'] ) ) : '';

		echo '<select name="jt_status"><option value="">' . esc_html__( 'All statuses', 'jaytech-support' ) . '</option>';
		foreach ( JT_Data::statuses() as $value => $label ) {
			printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $value ), selected( $current, $value, false ), esc_html( $label ) );
		}
		echo '</select>';
	}

	/**
	 * Apply the status filter to the admin query.
	 *
	 * @param WP_Query $query Query.
	 */
	public static function apply_status_filter( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || JT_Data::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$status = isset( $_GET['jt_status'] ) ? sanitize_key( wp_unslash( $_GET['jt_status'] ) ) : '';

		if ( isset( JT_Data::statuses()[ $status ] ) ) {
			$query->set(
				'meta_query',
				array(
					array(
						'key'   => '_jt_status',
						'value' => $status,
					),
				)
			);
		}
	}
}
