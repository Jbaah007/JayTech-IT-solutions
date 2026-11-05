<?php
/**
 * Frontend shortcode and ticket submission.
 *
 * @package JayTech_Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customer-facing form and ticket list.
 */
class JT_Frontend {

	/**
	 * Hook into WordPress.
	 */
	public static function init() {
		add_shortcode( 'jaytech_support_form', array( __CLASS__, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
		// Only logged-in users: no admin_post_nopriv_ handler is registered.
		add_action( 'admin_post_jt_submit_ticket', array( __CLASS__, 'handle_submission' ) );
	}

	/**
	 * Register the stylesheet (enqueued by the shortcode).
	 */
	public static function register_assets() {
		wp_register_style( 'jaytech-support', JT_SUPPORT_URL . 'assets/css/support.css', array(), JT_SUPPORT_VERSION );
	}

	/**
	 * Shortcode output.
	 *
	 * @return string
	 */
	public static function render_shortcode() {
		wp_enqueue_style( 'jaytech-support' );

		ob_start();
		echo '<div class="jt-support">';

		if ( ! is_user_logged_in() ) {
			printf(
				'<p class="jt-notice jt-notice--info">%s <a href="%s">%s</a></p>',
				esc_html__( 'Please log in to submit and view support tickets.', 'jaytech-support' ),
				esc_url( wp_login_url( get_permalink() ) ),
				esc_html__( 'Log in', 'jaytech-support' )
			);
			echo '</div>';
			return ob_get_clean();
		}

		self::render_notice();
		self::render_form();
		self::render_ticket_list();

		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * Show a result notice after submission.
	 */
	public static function render_notice() {
		// Display-only message key, validated against a whitelist.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$code = isset( $_GET['jt_notice'] ) ? sanitize_key( wp_unslash( $_GET['jt_notice'] ) ) : '';

		$messages = array(
			'success' => array( 'success', __( 'Your ticket has been submitted. We will respond soon.', 'jaytech-support' ) ),
			'invalid' => array( 'error', __( 'Please complete all fields correctly (subject up to 150 characters, description at least 10 characters).', 'jaytech-support' ) ),
			'failed'  => array( 'error', __( 'Your ticket could not be saved. Please try again.', 'jaytech-support' ) ),
		);

		if ( isset( $messages[ $code ] ) ) {
			printf(
				'<p class="jt-notice jt-notice--%s" role="status">%s</p>',
				esc_attr( $messages[ $code ][0] ),
				esc_html( $messages[ $code ][1] )
			);
		}
	}

	/**
	 * Submission form.
	 *
	 * @param string $redirect Optional URL to return to after submitting.
	 */
	public static function render_form( $redirect = '' ) {
		if ( '' === $redirect ) {
			$redirect = get_permalink();
		}
		?>
		<h2 class="jt-heading"><?php esc_html_e( 'Submit a Support Ticket', 'jaytech-support' ); ?></h2>
		<form class="jt-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="jt_submit_ticket">
			<input type="hidden" name="jt_redirect" value="<?php echo esc_url( $redirect ); ?>">
			<?php wp_nonce_field( 'jt_submit_ticket', 'jt_nonce' ); ?>

			<p>
				<label for="jt-subject"><?php esc_html_e( 'Subject', 'jaytech-support' ); ?></label>
				<input type="text" id="jt-subject" name="jt_subject" maxlength="150" required>
			</p>

			<div class="jt-row">
				<p>
					<label for="jt-category"><?php esc_html_e( 'Category', 'jaytech-support' ); ?></label>
					<select id="jt-category" name="jt_category" required>
						<?php foreach ( JT_Data::categories() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<p>
					<label for="jt-priority"><?php esc_html_e( 'Priority', 'jaytech-support' ); ?></label>
					<select id="jt-priority" name="jt_priority" required>
						<?php foreach ( JT_Data::priorities() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'medium' ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
			</div>

			<p>
				<label for="jt-description"><?php esc_html_e( 'Description', 'jaytech-support' ); ?></label>
				<textarea id="jt-description" name="jt_description" rows="6" minlength="10" required></textarea>
			</p>

			<p><button type="submit" class="jt-button"><?php esc_html_e( 'Submit Ticket', 'jaytech-support' ); ?></button></p>
		</form>
		<?php
	}

	/**
	 * List the current user's own tickets.
	 */
	private static function render_ticket_list() {
		$tickets = get_posts(
			array(
				'post_type'      => JT_Data::POST_TYPE,
				'author'         => get_current_user_id(),
				'posts_per_page' => 20,
				'post_status'    => 'publish',
			)
		);

		echo '<h2 class="jt-heading">' . esc_html__( 'Your Tickets', 'jaytech-support' ) . '</h2>';

		if ( ! $tickets ) {
			echo '<p>' . esc_html__( 'You have not submitted any tickets yet.', 'jaytech-support' ) . '</p>';
			return;
		}

		$statuses   = JT_Data::statuses();
		$categories = JT_Data::categories();
		$priorities = JT_Data::priorities();

		echo '<ul class="jt-tickets">';
		foreach ( $tickets as $ticket ) {
			$status = JT_Data::get_field( $ticket->ID, 'status' );
			printf(
				'<li class="jt-ticket"><div class="jt-ticket__head"><strong>%1$s</strong><span class="jt-badge jt-badge--%2$s">%3$s</span></div><div class="jt-ticket__meta">%4$s &middot; %5$s &middot; %6$s</div><div class="jt-ticket__body">%7$s</div></li>',
				esc_html( get_the_title( $ticket ) ),
				esc_attr( $status ),
				esc_html( $statuses[ $status ] ),
				esc_html( $categories[ JT_Data::get_field( $ticket->ID, 'category' ) ] ),
				esc_html( $priorities[ JT_Data::get_field( $ticket->ID, 'priority' ) ] ),
				esc_html( get_the_date( '', $ticket ) ),
				nl2br( esc_html( $ticket->post_content ) )
			);
		}
		echo '</ul>';
	}

	/**
	 * Handle the form POST.
	 */
	public static function handle_submission() {
		$redirect = isset( $_POST['jt_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['jt_redirect'] ) ) : home_url( '/' );

		if ( ! isset( $_POST['jt_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jt_nonce'] ) ), 'jt_submit_ticket' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'jaytech-support' ), '', array( 'response' => 403 ) );
		}

		if ( ! is_user_logged_in() || ! current_user_can( 'read' ) ) {
			wp_die( esc_html__( 'You must be logged in to submit a ticket.', 'jaytech-support' ), '', array( 'response' => 403 ) );
		}

		$subject     = isset( $_POST['jt_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['jt_subject'] ) ) : '';
		$description = isset( $_POST['jt_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['jt_description'] ) ) : '';
		$category    = isset( $_POST['jt_category'] ) ? sanitize_key( wp_unslash( $_POST['jt_category'] ) ) : '';
		$priority    = isset( $_POST['jt_priority'] ) ? sanitize_key( wp_unslash( $_POST['jt_priority'] ) ) : '';

		$valid = '' !== $subject
			&& mb_strlen( $subject ) <= 150
			&& mb_strlen( $description ) >= 10
			&& mb_strlen( $description ) <= 5000
			&& isset( JT_Data::categories()[ $category ] )
			&& isset( JT_Data::priorities()[ $priority ] );

		if ( ! $valid ) {
			self::redirect( $redirect, 'invalid' );
		}

		// Status is always forced to "open"; customers cannot choose it.
		$ticket_id = wp_insert_post(
			array(
				'post_type'    => JT_Data::POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => $subject,
				'post_content' => $description,
				'post_author'  => get_current_user_id(),
				'meta_input'   => array(
					'_jt_status'   => 'open',
					'_jt_category' => $category,
					'_jt_priority' => $priority,
				),
			),
			true
		);

		self::redirect( $redirect, is_wp_error( $ticket_id ) ? 'failed' : 'success' );
	}

	/**
	 * Safe redirect with a notice code, then exit.
	 *
	 * @param string $url  Target URL.
	 * @param string $code Notice code.
	 */
	private static function redirect( $url, $code ) {
		wp_safe_redirect( add_query_arg( 'jt_notice', $code, $url ) );
		exit;
	}
}
