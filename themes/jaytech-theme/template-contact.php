<?php
/**
 * Template Name: Contact Us
 *
 * @package JayTech_Theme
 */

get_header();

$jaytech_phone   = jaytech_mod( 'phone' );
$jaytech_email   = jaytech_mod( 'email' );
$jaytech_address = jaytech_mod( 'address' );
$jaytech_hours   = jaytech_mod( 'hours' );

while ( have_posts() ) :
	the_post();
	get_template_part(
		'template-parts/page-header',
		null,
		array( 'subtitle' => __( 'We would love to hear from you.', 'jaytech-theme' ) )
	);
	?>
	<section class="section">
		<div class="container">
			<div class="grid grid-4 contact-grid">
				<?php if ( '' !== $jaytech_phone ) : ?>
					<div class="card contact-card reveal">
						<span class="card-icon"><?php echo jaytech_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?></span>
						<h2><?php esc_html_e( 'Phone', 'jaytech-theme' ); ?></h2>
						<p><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $jaytech_phone ) ); ?>"><?php echo esc_html( $jaytech_phone ); ?></a></p>
					</div>
				<?php endif; ?>
				<?php if ( '' !== $jaytech_email ) : ?>
					<div class="card contact-card reveal">
						<span class="card-icon"><?php echo jaytech_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?></span>
						<h2><?php esc_html_e( 'Email', 'jaytech-theme' ); ?></h2>
						<p><a href="<?php echo esc_url( 'mailto:' . $jaytech_email ); ?>"><?php echo esc_html( $jaytech_email ); ?></a></p>
					</div>
				<?php endif; ?>
				<?php if ( '' !== $jaytech_address ) : ?>
					<div class="card contact-card reveal">
						<span class="card-icon"><?php echo jaytech_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?></span>
						<h2><?php esc_html_e( 'Address', 'jaytech-theme' ); ?></h2>
						<p><?php echo nl2br( esc_html( $jaytech_address ) ); ?></p>
					</div>
				<?php endif; ?>
				<?php if ( '' !== $jaytech_hours ) : ?>
					<div class="card contact-card reveal">
						<span class="card-icon"><?php echo jaytech_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?></span>
						<h2><?php esc_html_e( 'Hours', 'jaytech-theme' ); ?></h2>
						<p><?php echo esc_html( $jaytech_hours ); ?></p>
					</div>
				<?php endif; ?>
			</div>

			<div class="contact-form-wrap narrow reveal">
				<h2><?php esc_html_e( 'Send us a message', 'jaytech-theme' ); ?></h2>
				<?php jaytech_contact_form(); ?>
			</div>

			<?php if ( '' !== trim( get_the_content() ) ) : ?>
				<div class="entry-content narrow reveal"><?php the_content(); ?></div>
			<?php endif; ?>
		</div>
	</section>
	<?php
	get_template_part(
		'template-parts/cta',
		null,
		array(
			'title' => __( 'Need help right away?', 'jaytech-theme' ),
			'text'  => __( 'Existing customers can submit a ticket and track it from their dashboard.', 'jaytech-theme' ),
		)
	);
endwhile;

get_footer();
