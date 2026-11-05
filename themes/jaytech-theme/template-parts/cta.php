<?php
/**
 * Call-to-action band linking to the plugin-powered pages.
 *
 * @package JayTech_Theme
 *
 * @var array $args Optional: 'title', 'text'.
 */

$jaytech_cta_title = isset( $args['title'] ) ? $args['title'] : __( 'Ready to get started?', 'jaytech-theme' );
$jaytech_cta_text  = isset( $args['text'] ) ? $args['text'] : __( 'Book an appointment or submit a support ticket and we will take it from there.', 'jaytech-theme' );
?>
<section class="section cta">
	<div class="container cta-inner reveal">
		<h2><?php echo esc_html( $jaytech_cta_title ); ?></h2>
		<p><?php echo esc_html( $jaytech_cta_text ); ?></p>
		<div class="cta-actions">
			<a class="btn btn-primary" href="<?php echo esc_url( jaytech_page_url( 'book-appointment' ) ); ?>"><?php esc_html_e( 'Book an Appointment', 'jaytech-theme' ); ?></a>
			<a class="btn btn-dark" href="<?php echo esc_url( jaytech_page_url( 'support-tickets' ) ); ?>"><?php esc_html_e( 'Submit a Ticket', 'jaytech-theme' ); ?></a>
		</div>
	</div>
</section>
