<?php
/**
 * Customer dashboard shortcode.
 *
 * @package JayTech_Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dashboard with stats, ticket table, ticket detail, and new-ticket views.
 * Submissions reuse JT_Frontend::handle_submission().
 */
class JT_Dashboard {

	/**
	 * Hook into WordPress.
	 */
	public static function init() {
		add_shortcode( 'jaytech_customer_dashboard', array( __CLASS__, 'render_shortcode' ) );
	}

	/**
	 * Shortcode output.
	 *
	 * @return string
	 */
	public static function render_shortcode() {
		wp_enqueue_style( 'jaytech-support' );

		ob_start();
		echo '<div class="jt-support jt-dashboard">';

		if ( ! is_user_logged_in() ) {
			printf(
				'<p class="jt-notice jt-notice--info">%s <a href="%s">%s</a></p>',
				esc_html__( 'Please log in to access your support dashboard.', 'jaytech-support' ),
				esc_url( wp_login_url( get_permalink() ) ),
				esc_html__( 'Log in', 'jaytech-support' )
			);
			echo '</div>';
			return ob_get_clean();
		}

		// Read-only view selection; no state change.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$view      = isset( $_GET['jt_view'] ) ? sanitize_key( wp_unslash( $_GET['jt_view'] ) ) : '';
		$ticket_id = isset( $_GET['jt_ticket'] ) ? absint( $_GET['jt_ticket'] ) : 0;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		self::render_header( $view );
		JT_Frontend::render_notice();

		if ( 'new' === $view ) {
			JT_Frontend::render_form( add_query_arg( 'jt_view', 'new', get_permalink() ) );
		} elseif ( 'ticket' === $view ) {
			self::render_detail( $ticket_id );
		} else {
			self::render_overview();
		}

		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * Greeting and navigation buttons.
	 *
	 * @param string $view Current view.
	 */
	private static function render_header( $view ) {
		$user = wp_get_current_user();
		?>
		<div class="jt-dash-header">
			<div>
				<h2 class="jt-heading">
					<?php
					/* translators: %s: customer display name. */
					printf( esc_html__( 'Welcome, %s', 'jaytech-support' ), esc_html( $user->display_name ) );
					?>
				</h2>
				<p class="jt-muted"><?php esc_html_e( 'Track your support requests and submit new ones.', 'jaytech-support' ); ?></p>
			</div>
			<?php if ( 'new' === $view || 'ticket' === $view ) : ?>
				<a class="jt-button jt-button--outline" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Back to Dashboard', 'jaytech-support' ); ?></a>
			<?php else : ?>
				<a class="jt-button" href="<?php echo esc_url( add_query_arg( 'jt_view', 'new', get_permalink() ) ); ?>"><?php esc_html_e( 'New Ticket', 'jaytech-support' ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Stats cards and ticket table.
	 */
	private static function render_overview() {
		$tickets = get_posts(
			array(
				'post_type'      => JT_Data::POST_TYPE,
				'author'         => get_current_user_id(),
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		$counts = array_fill_keys( array_keys( JT_Data::statuses() ), 0 );
		foreach ( $tickets as $ticket ) {
			++$counts[ JT_Data::get_field( $ticket->ID, 'status' ) ];
		}

		$cards = array(
			array( 'total', __( 'Total Tickets', 'jaytech-support' ), count( $tickets ) ),
			array( 'open', __( 'Open', 'jaytech-support' ), $counts['open'] ),
			array( 'in_progress', __( 'In Progress', 'jaytech-support' ), $counts['in_progress'] ),
			array( 'resolved', __( 'Resolved', 'jaytech-support' ), $counts['resolved'] ),
		);

		echo '<div class="jt-stats">';
		foreach ( $cards as $card ) {
			printf(
				'<div class="jt-stat jt-stat--%1$s"><span class="jt-stat__number">%2$s</span><span class="jt-stat__label">%3$s</span></div>',
				esc_attr( $card[0] ),
				esc_html( number_format_i18n( $card[2] ) ),
				esc_html( $card[1] )
			);
		}
		echo '</div>';

		echo '<h3 class="jt-heading">' . esc_html__( 'Your Tickets', 'jaytech-support' ) . '</h3>';

		if ( ! $tickets ) {
			echo '<p>' . esc_html__( 'You have not submitted any tickets yet.', 'jaytech-support' ) . '</p>';
			return;
		}

		$statuses   = JT_Data::statuses();
		$categories = JT_Data::categories();
		$priorities = JT_Data::priorities();
		?>
		<div class="jt-table-wrap">
			<table class="jt-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Subject', 'jaytech-support' ); ?></th>
						<th><?php esc_html_e( 'Category', 'jaytech-support' ); ?></th>
						<th><?php esc_html_e( 'Priority', 'jaytech-support' ); ?></th>
						<th><?php esc_html_e( 'Status', 'jaytech-support' ); ?></th>
						<th><?php esc_html_e( 'Submitted', 'jaytech-support' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $tickets as $ticket ) : ?>
						<?php $status = JT_Data::get_field( $ticket->ID, 'status' ); ?>
						<tr>
							<td data-label="<?php esc_attr_e( 'Subject', 'jaytech-support' ); ?>">
								<a href="<?php echo esc_url( add_query_arg( array( 'jt_view' => 'ticket', 'jt_ticket' => $ticket->ID ), get_permalink() ) ); ?>"><?php echo esc_html( get_the_title( $ticket ) ); ?></a>
							</td>
							<td data-label="<?php esc_attr_e( 'Category', 'jaytech-support' ); ?>"><?php echo esc_html( $categories[ JT_Data::get_field( $ticket->ID, 'category' ) ] ); ?></td>
							<td data-label="<?php esc_attr_e( 'Priority', 'jaytech-support' ); ?>"><?php echo esc_html( $priorities[ JT_Data::get_field( $ticket->ID, 'priority' ) ] ); ?></td>
							<td data-label="<?php esc_attr_e( 'Status', 'jaytech-support' ); ?>"><span class="jt-badge jt-badge--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $statuses[ $status ] ); ?></span></td>
							<td data-label="<?php esc_attr_e( 'Submitted', 'jaytech-support' ); ?>"><?php echo esc_html( get_the_date( '', $ticket ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Single ticket detail; only the owner may view it.
	 *
	 * @param int $ticket_id Ticket ID.
	 */
	private static function render_detail( $ticket_id ) {
		$ticket = $ticket_id ? get_post( $ticket_id ) : null;

		// Same message for missing and foreign tickets to avoid leaking existence.
		if ( ! $ticket
			|| JT_Data::POST_TYPE !== $ticket->post_type
			|| 'publish' !== $ticket->post_status
			|| (int) $ticket->post_author !== get_current_user_id() ) {
			echo '<p class="jt-notice jt-notice--error">' . esc_html__( 'Ticket not found.', 'jaytech-support' ) . '</p>';
			return;
		}

		$status = JT_Data::get_field( $ticket->ID, 'status' );
		$labels = array(
			__( 'Category', 'jaytech-support' ) => JT_Data::categories()[ JT_Data::get_field( $ticket->ID, 'category' ) ],
			__( 'Priority', 'jaytech-support' ) => JT_Data::priorities()[ JT_Data::get_field( $ticket->ID, 'priority' ) ],
			__( 'Submitted', 'jaytech-support' ) => get_the_date( '', $ticket ),
		);
		?>
		<article class="jt-detail">
			<div class="jt-ticket__head">
				<h3><?php echo esc_html( get_the_title( $ticket ) ); ?></h3>
				<span class="jt-badge jt-badge--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( JT_Data::statuses()[ $status ] ); ?></span>
			</div>
			<dl class="jt-detail__meta">
				<?php foreach ( $labels as $label => $value ) : ?>
					<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
			<div class="jt-ticket__body"><?php echo nl2br( esc_html( $ticket->post_content ) ); ?></div>
		</article>
		<?php
	}
}
