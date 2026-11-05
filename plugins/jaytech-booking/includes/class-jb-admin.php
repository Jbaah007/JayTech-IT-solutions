<?php
/**
 * Admin interface: details, status, list columns, settings.
 *
 * @package JayTech_Booking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin-side appointment management.
 */
class JB_Admin {

	/**
	 * Hook into WordPress.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_' . JB_Data::POST_TYPE, array( __CLASS__, 'save_status' ) );
		add_filter( 'manage_' . JB_Data::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . JB_Data::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( __CLASS__, 'status_filter' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'adjust_query' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Register meta boxes.
	 */
	public static function add_meta_boxes() {
		add_meta_box( 'jb_details', __( 'Appointment Details', 'jaytech-booking' ), array( __CLASS__, 'render_details' ), JB_Data::POST_TYPE, 'normal', 'high' );
		add_meta_box( 'jb_status', __( 'Status', 'jaytech-booking' ), array( __CLASS__, 'render_status' ), JB_Data::POST_TYPE, 'side', 'high' );
	}

	/**
	 * Read-only appointment details.
	 *
	 * @param WP_Post $post Appointment.
	 */
	public static function render_details( $post ) {
		$services = JB_Data::services();
		$start    = (int) get_post_meta( $post->ID, '_jb_start', true );
		$end      = (int) get_post_meta( $post->ID, '_jb_end', true );
		$email    = get_post_meta( $post->ID, '_jb_email', true );

		$rows = array(
			__( 'Customer', 'jaytech-booking' ) => esc_html( get_post_meta( $post->ID, '_jb_name', true ) ),
			__( 'Email', 'jaytech-booking' )    => '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>',
			__( 'Service', 'jaytech-booking' )  => esc_html( $services[ JB_Data::get_field( $post->ID, 'service' ) ] ),
			__( 'Starts', 'jaytech-booking' )   => esc_html( JB_Data::format_time( $start ) ),
			__( 'Ends', 'jaytech-booking' )     => esc_html( JB_Data::format_time( $end ) ),
			__( 'Request', 'jaytech-booking' )  => nl2br( esc_html( $post->post_content ) ),
		);

		echo '<table class="form-table"><tbody>';
		foreach ( $rows as $label => $html ) {
			// Values are escaped when the $rows array is built.
			echo '<tr><th>' . esc_html( $label ) . '</th><td>' . $html . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</tbody></table>';
	}

	/**
	 * Status selector.
	 *
	 * @param WP_Post $post Appointment.
	 */
	public static function render_status( $post ) {
		wp_nonce_field( 'jb_save_status_' . $post->ID, 'jb_status_nonce' );
		$current = JB_Data::get_field( $post->ID, 'status' );

		echo '<select name="jb_status" class="widefat">';
		foreach ( JB_Data::statuses() as $value => $label ) {
			printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $value ), selected( $current, $value, false ), esc_html( $label ) );
		}
		echo '</select>';
	}

	/**
	 * Save the status.
	 *
	 * @param int $post_id Appointment ID.
	 */
	public static function save_status( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['jb_status_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jb_status_nonce'] ) ), 'jb_save_status_' . $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) || ! isset( $_POST['jb_status'] ) ) {
			return;
		}

		$status = sanitize_key( wp_unslash( $_POST['jb_status'] ) );
		if ( isset( JB_Data::statuses()[ $status ] ) ) {
			update_post_meta( $post_id, '_jb_status', $status );
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
			'cb'         => $columns['cb'],
			'title'      => __( 'Appointment', 'jaytech-booking' ),
			'jb_contact' => __( 'Customer', 'jaytech-booking' ),
			'jb_service' => __( 'Service', 'jaytech-booking' ),
			'jb_when'    => __( 'Date & Time', 'jaytech-booking' ),
			'jb_status'  => __( 'Status', 'jaytech-booking' ),
		);
	}

	/**
	 * Render a list table cell.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Appointment ID.
	 */
	public static function column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'jb_contact':
				echo esc_html( get_post_meta( $post_id, '_jb_name', true ) ) . '<br>' . esc_html( get_post_meta( $post_id, '_jb_email', true ) );
				break;
			case 'jb_service':
				echo esc_html( JB_Data::services()[ JB_Data::get_field( $post_id, 'service' ) ] );
				break;
			case 'jb_when':
				echo esc_html( JB_Data::format_time( (int) get_post_meta( $post_id, '_jb_start', true ) ) );
				break;
			case 'jb_status':
				echo esc_html( JB_Data::statuses()[ JB_Data::get_field( $post_id, 'status' ) ] );
				break;
		}
	}

	/**
	 * Status dropdown above the list table.
	 *
	 * @param string $post_type Current post type.
	 */
	public static function status_filter( $post_type ) {
		if ( JB_Data::POST_TYPE !== $post_type ) {
			return;
		}

		// Read-only list filter.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current = isset( $_GET['jb_status'] ) ? sanitize_key( wp_unslash( $_GET['jb_status'] ) ) : '';

		echo '<select name="jb_status"><option value="">' . esc_html__( 'All statuses', 'jaytech-booking' ) . '</option>';
		foreach ( JB_Data::statuses() as $value => $label ) {
			printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $value ), selected( $current, $value, false ), esc_html( $label ) );
		}
		echo '</select>';
	}

	/**
	 * Sort by appointment time and apply the status filter in the admin list.
	 *
	 * @param WP_Query $query Query.
	 */
	public static function adjust_query( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || JB_Data::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}

		if ( ! $query->get( 'orderby' ) ) {
			$query->set( 'meta_key', '_jb_start' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'DESC' );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$status = isset( $_GET['jb_status'] ) ? sanitize_key( wp_unslash( $_GET['jb_status'] ) ) : '';
		if ( isset( JB_Data::statuses()[ $status ] ) ) {
			$query->set( 'meta_query', array( array( 'key' => '_jb_status', 'value' => $status ) ) );
		}
	}

	/**
	 * Settings submenu under Appointments.
	 */
	public static function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=' . JB_Data::POST_TYPE,
			__( 'Booking Settings', 'jaytech-booking' ),
			__( 'Settings', 'jaytech-booking' ),
			'manage_options',
			'jb-settings',
			array( __CLASS__, 'render_settings' )
		);
	}

	/**
	 * Register settings.
	 */
	public static function register_settings() {
		register_setting( 'jb_settings', 'jb_duration', array( 'type' => 'integer', 'sanitize_callback' => array( __CLASS__, 'sanitize_duration' ), 'default' => 60 ) );
		register_setting( 'jb_settings', 'jb_open_hour', array( 'type' => 'integer', 'sanitize_callback' => array( __CLASS__, 'sanitize_open' ), 'default' => 9 ) );
		register_setting( 'jb_settings', 'jb_close_hour', array( 'type' => 'integer', 'sanitize_callback' => array( __CLASS__, 'sanitize_close' ), 'default' => 17 ) );
	}

	/**
	 * Clamp duration to 15-480 minutes.
	 *
	 * @param mixed $value Raw value.
	 * @return int
	 */
	public static function sanitize_duration( $value ) {
		return max( 15, min( 480, absint( $value ) ) );
	}

	/**
	 * Clamp opening hour to 0-23.
	 *
	 * @param mixed $value Raw value.
	 * @return int
	 */
	public static function sanitize_open( $value ) {
		return max( 0, min( 23, absint( $value ) ) );
	}

	/**
	 * Clamp closing hour to 1-24.
	 *
	 * @param mixed $value Raw value.
	 * @return int
	 */
	public static function sanitize_close( $value ) {
		return max( 1, min( 24, absint( $value ) ) );
	}

	/**
	 * Render the settings page.
	 */
	public static function render_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Booking Settings', 'jaytech-booking' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'jb_settings' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="jb_duration"><?php esc_html_e( 'Appointment duration (minutes)', 'jaytech-booking' ); ?></label></th>
						<td><input type="number" id="jb_duration" name="jb_duration" min="15" max="480" step="5" value="<?php echo esc_attr( JB_Data::duration() ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="jb_open_hour"><?php esc_html_e( 'Opening hour (0-23)', 'jaytech-booking' ); ?></label></th>
						<td><input type="number" id="jb_open_hour" name="jb_open_hour" min="0" max="23" value="<?php echo esc_attr( JB_Data::open_hour() ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="jb_close_hour"><?php esc_html_e( 'Closing hour (1-24)', 'jaytech-booking' ); ?></label></th>
						<td><input type="number" id="jb_close_hour" name="jb_close_hour" min="1" max="24" value="<?php echo esc_attr( JB_Data::close_hour() ); ?>"></td>
					</tr>
				</table>
				<p class="description">
					<?php
					/* translators: %s: timezone name. */
					printf( esc_html__( 'Times use the site timezone (%s). Changing the duration affects new bookings only.', 'jaytech-booking' ), esc_html( wp_timezone_string() ) );
					?>
				</p>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
