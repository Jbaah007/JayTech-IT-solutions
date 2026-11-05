<?php
/**
 * Frontend booking form, customer appointment list, and submission handler.
 *
 * @package JayTech_Booking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode [jaytech_booking_form].
 */
class JB_Frontend {

	/**
	 * Hook into WordPress.
	 */
	public static function init() {
		add_shortcode( 'jaytech_booking_form', array( __CLASS__, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
		// Logged-in only: no admin_post_nopriv_ handler is registered.
		add_action( 'admin_post_jb_submit_booking', array( __CLASS__, 'handle_submission' ) );
	}

	/**
	 * Register the stylesheet (enqueued by the shortcode).
	 */
	public static function register_assets() {
		wp_register_style( 'jaytech-booking', JB_URL . 'assets/css/booking.css', array(), JB_VERSION );
	}

	/**
	 * Shortcode output.
	 *
	 * @return string
	 */
	public static function render_shortcode() {
		wp_enqueue_style( 'jaytech-booking' );

		ob_start();
		echo '<div class="jb-booking">';

		if ( ! is_user_logged_in() ) {
			printf(
				'<p class="jb-notice jb-notice--info">%s <a href="%s">%s</a></p>',
				esc_html__( 'Please log in to book and view appointments.', 'jaytech-booking' ),
				esc_url( wp_login_url( get_permalink() ) ),
				esc_html__( 'Log in', 'jaytech-booking' )
			);
			echo '</div>';
			return ob_get_clean();
		}

		$state = get_transient( 'jb_form_' . get_current_user_id() );
		if ( $state ) {
			delete_transient( 'jb_form_' . get_current_user_id() );
		}
		$errors = ! empty( $state['errors'] ) ? (array) $state['errors'] : array();
		$old    = ! empty( $state['old'] ) ? (array) $state['old'] : array();

		// Display-only flag.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['jb_notice'] ) && 'success' === sanitize_key( wp_unslash( $_GET['jb_notice'] ) ) ) {
			echo '<p class="jb-notice jb-notice--success" role="status">' . esc_html__( 'Your appointment request has been received. We will confirm it shortly.', 'jaytech-booking' ) . '</p>';
		}

		if ( $errors ) {
			echo '<div class="jb-notice jb-notice--error" role="alert"><ul>';
			foreach ( $errors as $error ) {
				echo '<li>' . esc_html( $error ) . '</li>';
			}
			echo '</ul></div>';
		}

		self::render_form( $old );
		self::render_appointments();

		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * Booking form.
	 *
	 * @param array $old Previously submitted values.
	 */
	private static function render_form( $old ) {
		$user = wp_get_current_user();
		$val  = function ( $key, $default = '' ) use ( $old ) {
			return isset( $old[ $key ] ) ? $old[ $key ] : $default;
		};

		$duration = JB_Data::duration();
		?>
		<h2 class="jb-heading"><?php esc_html_e( 'Book an Appointment', 'jaytech-booking' ); ?></h2>
		<form class="jb-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="jb_submit_booking">
			<input type="hidden" name="jb_redirect" value="<?php echo esc_url( get_permalink() ); ?>">
			<?php wp_nonce_field( 'jb_submit_booking', 'jb_nonce' ); ?>

			<div class="jb-row">
				<p>
					<label for="jb-name"><?php esc_html_e( 'Full name', 'jaytech-booking' ); ?></label>
					<input type="text" id="jb-name" name="jb_name" maxlength="100" required value="<?php echo esc_attr( $val( 'name', $user->display_name ) ); ?>">
				</p>
				<p>
					<label for="jb-email"><?php esc_html_e( 'Email', 'jaytech-booking' ); ?></label>
					<input type="email" id="jb-email" name="jb_email" maxlength="100" required value="<?php echo esc_attr( $val( 'email', $user->user_email ) ); ?>">
				</p>
			</div>

			<p>
				<label for="jb-service"><?php esc_html_e( 'Service', 'jaytech-booking' ); ?></label>
				<select id="jb-service" name="jb_service" required>
					<?php foreach ( JB_Data::services() as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $val( 'service' ), $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<div class="jb-row">
				<p>
					<label for="jb-date"><?php esc_html_e( 'Preferred date', 'jaytech-booking' ); ?></label>
					<input type="date" id="jb-date" name="jb_date" required min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" value="<?php echo esc_attr( $val( 'date' ) ); ?>">
				</p>
				<p>
					<label for="jb-time"><?php esc_html_e( 'Preferred time', 'jaytech-booking' ); ?></label>
					<input type="time" id="jb-time" name="jb_time" required step="900" value="<?php echo esc_attr( $val( 'time' ) ); ?>">
				</p>
			</div>

			<p class="jb-hint">
				<?php
				/* translators: 1: duration in minutes, 2: opening hour, 3: closing hour, 4: timezone. */
				printf(
					esc_html__( 'Appointments last %1$d minutes. Available %2$02d:00-%3$02d:00 (%4$s).', 'jaytech-booking' ),
					(int) $duration,
					(int) JB_Data::open_hour(),
					(int) JB_Data::close_hour(),
					esc_html( wp_timezone_string() )
				);
				?>
			</p>

			<p>
				<label for="jb-description"><?php esc_html_e( 'Describe your request', 'jaytech-booking' ); ?></label>
				<textarea id="jb-description" name="jb_description" rows="5" minlength="10" maxlength="2000" required><?php echo esc_textarea( $val( 'description' ) ); ?></textarea>
			</p>

			<p><button type="submit" class="jb-button"><?php esc_html_e( 'Request Appointment', 'jaytech-booking' ); ?></button></p>
		</form>
		<?php
	}

	/**
	 * The current user's own appointments.
	 */
	private static function render_appointments() {
		$appointments = get_posts(
			array(
				'post_type'      => JB_Data::POST_TYPE,
				'post_status'    => 'publish',
				'author'         => get_current_user_id(),
				'posts_per_page' => 50,
				'meta_key'       => '_jb_start',
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
			)
		);

		echo '<h2 class="jb-heading">' . esc_html__( 'Your Appointments', 'jaytech-booking' ) . '</h2>';

		if ( ! $appointments ) {
			echo '<p>' . esc_html__( 'You have no appointments yet.', 'jaytech-booking' ) . '</p>';
			return;
		}

		$services = JB_Data::services();
		$statuses = JB_Data::statuses();

		echo '<ul class="jb-list">';
		foreach ( $appointments as $appointment ) {
			$status = JB_Data::get_field( $appointment->ID, 'status' );
			printf(
				'<li class="jb-item"><div class="jb-item__head"><strong>%1$s</strong><span class="jb-badge jb-badge--%2$s">%3$s</span></div><div class="jb-item__meta">%4$s</div><div class="jb-item__body">%5$s</div></li>',
				esc_html( $services[ JB_Data::get_field( $appointment->ID, 'service' ) ] ),
				esc_attr( $status ),
				esc_html( $statuses[ $status ] ),
				esc_html( JB_Data::format_time( (int) get_post_meta( $appointment->ID, '_jb_start', true ) ) ),
				nl2br( esc_html( $appointment->post_content ) )
			);
		}
		echo '</ul>';
	}

	/**
	 * Handle the form POST, then redirect (Post/Redirect/Get).
	 */
	public static function handle_submission() {
		$redirect = isset( $_POST['jb_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['jb_redirect'] ) ) : home_url( '/' );

		if ( ! isset( $_POST['jb_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jb_nonce'] ) ), 'jb_submit_booking' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'jaytech-booking' ), '', array( 'response' => 403 ) );
		}

		if ( ! is_user_logged_in() || ! current_user_can( 'read' ) ) {
			wp_die( esc_html__( 'You must be logged in to book an appointment.', 'jaytech-booking' ), '', array( 'response' => 403 ) );
		}

		$old = array(
			'name'        => isset( $_POST['jb_name'] ) ? sanitize_text_field( wp_unslash( $_POST['jb_name'] ) ) : '',
			'email'       => isset( $_POST['jb_email'] ) ? sanitize_email( wp_unslash( $_POST['jb_email'] ) ) : '',
			'service'     => isset( $_POST['jb_service'] ) ? sanitize_key( wp_unslash( $_POST['jb_service'] ) ) : '',
			'date'        => isset( $_POST['jb_date'] ) ? sanitize_text_field( wp_unslash( $_POST['jb_date'] ) ) : '',
			'time'        => isset( $_POST['jb_time'] ) ? sanitize_text_field( wp_unslash( $_POST['jb_time'] ) ) : '',
			'description' => isset( $_POST['jb_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['jb_description'] ) ) : '',
		);

		$errors = array();

		if ( '' === $old['name'] || mb_strlen( $old['name'] ) > 100 ) {
			$errors[] = __( 'Please enter your name (up to 100 characters).', 'jaytech-booking' );
		}
		if ( ! is_email( $old['email'] ) ) {
			$errors[] = __( 'Please enter a valid email address.', 'jaytech-booking' );
		}
		if ( ! isset( JB_Data::services()[ $old['service'] ] ) ) {
			$errors[] = __( 'Please choose a service.', 'jaytech-booking' );
		}
		if ( mb_strlen( $old['description'] ) < 10 || mb_strlen( $old['description'] ) > 2000 ) {
			$errors[] = __( 'Please describe your request (10 to 2000 characters).', 'jaytech-booking' );
		}

		$start = self::parse_start( $old['date'], $old['time'] );
		if ( ! $start ) {
			$errors[] = __( 'Please choose a valid date and time.', 'jaytech-booking' );
		} else {
			$end    = $start->getTimestamp() + JB_Data::duration() * MINUTE_IN_SECONDS;
			$errors = array_merge( $errors, self::check_schedule( $start, $end ) );
		}

		if ( $errors ) {
			self::fail( $redirect, $errors, $old );
		}

		$start_ts = $start->getTimestamp();

		if ( ! self::acquire_lock() ) {
			self::fail( $redirect, array( __( 'The system is busy. Please try again.', 'jaytech-booking' ) ), $old );
		}

		// Re-check inside the lock so two simultaneous requests cannot take the same slot.
		if ( self::slot_taken( $start_ts, $end ) ) {
			self::release_lock();
			self::fail( $redirect, array( __( 'That time slot is no longer available. Please choose another.', 'jaytech-booking' ) ), $old );
		}

		$services = JB_Data::services();
		$id       = wp_insert_post(
			array(
				'post_type'    => JB_Data::POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => sprintf( '%s - %s - %s', $services[ $old['service'] ], $old['name'], $start->format( 'Y-m-d H:i' ) ),
				'post_content' => $old['description'],
				'post_author'  => get_current_user_id(),
				'meta_input'   => array(
					'_jb_name'    => $old['name'],
					'_jb_email'   => $old['email'],
					'_jb_service' => $old['service'],
					'_jb_start'   => $start_ts,
					'_jb_end'     => $end,
					'_jb_status'  => 'pending',
				),
			),
			true
		);

		self::release_lock();

		if ( is_wp_error( $id ) ) {
			self::fail( $redirect, array( __( 'Your appointment could not be saved. Please try again.', 'jaytech-booking' ) ), $old );
		}

		wp_safe_redirect( add_query_arg( 'jb_notice', 'success', $redirect ) );
		exit;
	}

	/**
	 * Parse a date and time in the site timezone.
	 *
	 * @param string $date Y-m-d.
	 * @param string $time H:i.
	 * @return DateTimeImmutable|false
	 */
	private static function parse_start( $date, $time ) {
		$dt = DateTimeImmutable::createFromFormat( '!Y-m-d H:i', $date . ' ' . $time, wp_timezone() );
		return ( $dt && $dt->format( 'Y-m-d H:i' ) === $date . ' ' . $time ) ? $dt : false;
	}

	/**
	 * Validate the slot is in the future, within a year, and inside business hours.
	 *
	 * @param DateTimeImmutable $start Start.
	 * @param int               $end   End timestamp.
	 * @return array Error messages.
	 */
	private static function check_schedule( $start, $end ) {
		$errors = array();
		$ts     = $start->getTimestamp();

		if ( $ts <= time() ) {
			$errors[] = __( 'Appointments cannot be booked in the past.', 'jaytech-booking' );
		} elseif ( $ts > time() + YEAR_IN_SECONDS ) {
			$errors[] = __( 'Appointments can be booked up to one year ahead.', 'jaytech-booking' );
		}

		$day   = $start->setTime( 0, 0 );
		$open  = $day->modify( '+' . JB_Data::open_hour() . ' hours' )->getTimestamp();
		$close = $day->modify( '+' . JB_Data::close_hour() . ' hours' )->getTimestamp();

		if ( $ts < $open || $end > $close ) {
			$errors[] = sprintf(
				/* translators: 1: opening hour, 2: closing hour. */
				__( 'Appointments must fall between %1$02d:00 and %2$02d:00.', 'jaytech-booking' ),
				JB_Data::open_hour(),
				JB_Data::close_hour()
			);
		}

		if ( ! $errors && self::slot_taken( $ts, $end ) ) {
			$errors[] = __( 'That time slot is already booked. Please choose another.', 'jaytech-booking' );
		}

		return $errors;
	}

	/**
	 * Whether a non-cancelled appointment overlaps the given range.
	 *
	 * @param int $start Start timestamp.
	 * @param int $end   End timestamp.
	 * @return bool
	 */
	private static function slot_taken( $start, $end ) {
		$found = get_posts(
			array(
				'post_type'      => JB_Data::POST_TYPE,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'meta_query'     => array(
					'relation' => 'AND',
					array( 'key' => '_jb_start', 'value' => $end, 'compare' => '<', 'type' => 'NUMERIC' ),
					array( 'key' => '_jb_end', 'value' => $start, 'compare' => '>', 'type' => 'NUMERIC' ),
					array( 'key' => '_jb_status', 'value' => 'cancelled', 'compare' => '!=' ),
				),
			)
		);

		return ! empty( $found );
	}

	/**
	 * Take a short-lived lock using the atomic option insert.
	 *
	 * @return bool
	 */
	private static function acquire_lock() {
		if ( add_option( 'jb_booking_lock', time(), '', 'no' ) ) {
			return true;
		}

		// Clear a stale lock left by a crashed request.
		if ( (int) get_option( 'jb_booking_lock' ) < time() - 15 ) {
			delete_option( 'jb_booking_lock' );
			return add_option( 'jb_booking_lock', time(), '', 'no' );
		}

		return false;
	}

	/**
	 * Release the lock.
	 */
	private static function release_lock() {
		delete_option( 'jb_booking_lock' );
	}

	/**
	 * Save errors and input for the next page load, then redirect.
	 *
	 * @param string $redirect Target URL.
	 * @param array  $errors   Messages.
	 * @param array  $old      Submitted values.
	 */
	private static function fail( $redirect, $errors, $old ) {
		set_transient(
			'jb_form_' . get_current_user_id(),
			array(
				'errors' => $errors,
				'old'    => $old,
			),
			5 * MINUTE_IN_SECONDS
		);

		wp_safe_redirect( add_query_arg( 'jb_notice', 'error', $redirect ) );
		exit;
	}
}
