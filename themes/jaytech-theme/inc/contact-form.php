<?php
/**
 * Contact form: rendering and secure submission (emails the site contact address).
 *
 * @package JayTech_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_post_nopriv_jaytech_contact', 'jaytech_handle_contact' );
add_action( 'admin_post_jaytech_contact', 'jaytech_handle_contact' );

/**
 * Store errors and input for the next page load, then redirect.
 *
 * @param string $redirect Target URL.
 * @param array  $errors   Messages.
 * @param array  $old      Submitted values.
 */
function jaytech_contact_fail( $redirect, $errors, $old ) {
	$token = wp_generate_password( 12, false );
	set_transient(
		'jaytech_cf_' . $token,
		array(
			'errors' => $errors,
			'old'    => $old,
		),
		10 * MINUTE_IN_SECONDS
	);

	wp_safe_redirect( add_query_arg( 'jt_contact', $token, $redirect ) . '#contact-form' );
	exit;
}

/**
 * Handle the contact form POST, then redirect (Post/Redirect/Get).
 */
function jaytech_handle_contact() {
	$redirect = isset( $_POST['jt_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['jt_redirect'] ) ) : home_url( '/' );

	if ( ! isset( $_POST['jt_cf_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jt_cf_nonce'] ) ), 'jaytech_contact' ) ) {
		wp_die( esc_html__( 'Security check failed. Please go back and try again.', 'jaytech-theme' ), '', array( 'response' => 403 ) );
	}

	// Honeypot: bots fill the hidden field. Pretend success.
	if ( ! empty( $_POST['jt_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'jt_contact', 'sent', $redirect ) . '#contact-form' );
		exit;
	}

	$old = array(
		'name'    => isset( $_POST['jt_name'] ) ? sanitize_text_field( wp_unslash( $_POST['jt_name'] ) ) : '',
		'email'   => isset( $_POST['jt_email'] ) ? sanitize_email( wp_unslash( $_POST['jt_email'] ) ) : '',
		'subject' => isset( $_POST['jt_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['jt_subject'] ) ) : '',
		'message' => isset( $_POST['jt_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['jt_message'] ) ) : '',
	);

	$errors = array();

	if ( '' === $old['name'] || mb_strlen( $old['name'] ) > 100 ) {
		$errors[] = __( 'Please enter your name (up to 100 characters).', 'jaytech-theme' );
	}
	if ( ! is_email( $old['email'] ) ) {
		$errors[] = __( 'Please enter a valid email address.', 'jaytech-theme' );
	}
	if ( mb_strlen( $old['subject'] ) > 150 ) {
		$errors[] = __( 'The subject must be 150 characters or fewer.', 'jaytech-theme' );
	}
	if ( mb_strlen( $old['message'] ) < 10 || mb_strlen( $old['message'] ) > 3000 ) {
		$errors[] = __( 'Please write a message between 10 and 3000 characters.', 'jaytech-theme' );
	}

	$ip       = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rate_key = 'jaytech_cf_rl_' . md5( $ip );

	if ( ! $errors && get_transient( $rate_key ) ) {
		$errors[] = __( 'Please wait a minute before sending another message.', 'jaytech-theme' );
	}

	if ( $errors ) {
		jaytech_contact_fail( $redirect, $errors, $old );
	}

	$to = jaytech_mod( 'email' );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$subject = '' !== $old['subject'] ? $old['subject'] : __( 'New contact message', 'jaytech-theme' );
	$body    = sprintf(
		"Name: %s\nEmail: %s\n\n%s",
		$old['name'],
		$old['email'],
		$old['message']
	);

	$sent = wp_mail(
		$to,
		/* translators: 1: site name, 2: message subject. */
		sprintf( __( '[%1$s] %2$s', 'jaytech-theme' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $subject ),
		$body,
		array( 'Reply-To: ' . $old['name'] . ' <' . $old['email'] . '>' )
	);

	if ( ! $sent ) {
		jaytech_contact_fail( $redirect, array( __( 'Your message could not be sent. Please try again or contact us by phone or email.', 'jaytech-theme' ) ), $old );
	}

	set_transient( $rate_key, 1, MINUTE_IN_SECONDS );

	wp_safe_redirect( add_query_arg( 'jt_contact', 'sent', $redirect ) . '#contact-form' );
	exit;
}

/**
 * Render the contact form with any success message, errors, and preserved input.
 */
function jaytech_contact_form() {
	$old    = array();
	$errors = array();
	$sent   = false;

	// Display-only token or flag from the redirect.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$flag = isset( $_GET['jt_contact'] ) ? sanitize_text_field( wp_unslash( $_GET['jt_contact'] ) ) : '';

	if ( 'sent' === $flag ) {
		$sent = true;
	} elseif ( preg_match( '/^[A-Za-z0-9]{12}$/', $flag ) ) {
		$state = get_transient( 'jaytech_cf_' . $flag );
		if ( $state ) {
			delete_transient( 'jaytech_cf_' . $flag );
			$old    = isset( $state['old'] ) ? (array) $state['old'] : array();
			$errors = isset( $state['errors'] ) ? (array) $state['errors'] : array();
		}
	}

	$val = function ( $key ) use ( $old ) {
		return isset( $old[ $key ] ) ? $old[ $key ] : '';
	};
	?>
	<div id="contact-form" class="contact-form-box">
		<?php if ( $sent ) : ?>
			<p class="form-notice form-notice--success" role="status"><?php esc_html_e( 'Thank you! Your message has been sent. We will get back to you soon.', 'jaytech-theme' ); ?></p>
		<?php endif; ?>

		<?php if ( $errors ) : ?>
			<div class="form-notice form-notice--error" role="alert">
				<ul>
					<?php foreach ( $errors as $error ) : ?>
						<li><?php echo esc_html( $error ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="jaytech_contact">
			<input type="hidden" name="jt_redirect" value="<?php echo esc_url( get_permalink() ); ?>">
			<?php wp_nonce_field( 'jaytech_contact', 'jt_cf_nonce' ); ?>

			<div class="form-hp" aria-hidden="true">
				<label for="jt-website"><?php esc_html_e( 'Leave this field empty', 'jaytech-theme' ); ?></label>
				<input type="text" id="jt-website" name="jt_website" tabindex="-1" autocomplete="off">
			</div>

			<div class="form-row">
				<p>
					<label for="jt-cf-name"><?php esc_html_e( 'Your name', 'jaytech-theme' ); ?></label>
					<input type="text" id="jt-cf-name" name="jt_name" maxlength="100" required value="<?php echo esc_attr( $val( 'name' ) ); ?>">
				</p>
				<p>
					<label for="jt-cf-email"><?php esc_html_e( 'Email address', 'jaytech-theme' ); ?></label>
					<input type="email" id="jt-cf-email" name="jt_email" maxlength="100" required value="<?php echo esc_attr( $val( 'email' ) ); ?>">
				</p>
			</div>
			<p>
				<label for="jt-cf-subject"><?php esc_html_e( 'Subject (optional)', 'jaytech-theme' ); ?></label>
				<input type="text" id="jt-cf-subject" name="jt_subject" maxlength="150" value="<?php echo esc_attr( $val( 'subject' ) ); ?>">
			</p>
			<p>
				<label for="jt-cf-message"><?php esc_html_e( 'Message', 'jaytech-theme' ); ?></label>
				<textarea id="jt-cf-message" name="jt_message" rows="6" minlength="10" maxlength="3000" required><?php echo esc_textarea( $val( 'message' ) ); ?></textarea>
			</p>
			<p><button type="submit" class="btn btn-primary"><?php esc_html_e( 'Send Message', 'jaytech-theme' ); ?></button></p>
		</form>
	</div>
	<?php
}
