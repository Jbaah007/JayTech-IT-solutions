<?php
/**
 * Homepage template.
 *
 * @package JayTech_Theme
 */

get_header();

$jaytech_reasons = array(
	array( 'zap', __( 'Responsive communication', 'jaytech-theme' ), __( 'Clear updates and quick replies so you always know where things stand.', 'jaytech-theme' ) ),
	array( 'shield', __( 'Security-minded', 'jaytech-theme' ), __( 'Sensible security practices are built into everything we set up.', 'jaytech-theme' ) ),
	array( 'users', __( 'Plain-language help', 'jaytech-theme' ), __( 'No jargon. We explain what we are doing and why.', 'jaytech-theme' ) ),
	array( 'check', __( 'Tailored solutions', 'jaytech-theme' ), __( 'Recommendations that fit your needs and budget, not a one-size-fits-all package.', 'jaytech-theme' ) ),
);

$jaytech_actions = array(
	array( __( 'Submit a support ticket', 'jaytech-theme' ), 'support-tickets' ),
	array( __( 'Book an appointment', 'jaytech-theme' ), 'book-appointment' ),
	array( __( 'View your dashboard', 'jaytech-theme' ), 'customer-dashboard' ),
);
?>

<section class="hero">
	<div class="container hero-inner">
		<div class="hero-text">
			<span class="eyebrow"><?php echo esc_html( jaytech_mod( 'hero_eyebrow' ) ); ?></span>
			<h1><?php echo esc_html( jaytech_mod( 'hero_title' ) ); ?></h1>
			<p><?php echo esc_html( jaytech_mod( 'hero_text' ) ); ?></p>
			<div class="hero-actions">
				<a class="btn btn-primary" href="<?php echo esc_url( jaytech_page_url( 'book-appointment' ) ); ?>"><?php esc_html_e( 'Book an Appointment', 'jaytech-theme' ); ?></a>
				<a class="btn btn-outline" href="<?php echo esc_url( jaytech_page_published( 'services' ) ? jaytech_page_url( 'services' ) : '#services' ); ?>"><?php esc_html_e( 'Our Services', 'jaytech-theme' ); ?></a>
			</div>
		</div>
		<div class="hero-visual">
			<div class="hero-card">
				<strong><?php esc_html_e( 'Customer portal', 'jaytech-theme' ); ?></strong>
				<ul class="hero-card-links">
					<?php foreach ( $jaytech_actions as $jaytech_action ) : ?>
						<li><a href="<?php echo esc_url( jaytech_page_url( $jaytech_action[1] ) ); ?>"><?php echo esc_html( $jaytech_action[0] ); ?><span aria-hidden="true">&rarr;</span></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container narrow intro reveal">
		<span class="eyebrow"><?php esc_html_e( 'About JayTech', 'jaytech-theme' ); ?></span>
		<h2><?php echo esc_html( jaytech_mod( 'intro_title' ) ); ?></h2>
		<p><?php echo esc_html( jaytech_mod( 'intro_text' ) ); ?></p>
		<?php if ( jaytech_page_published( 'about-us' ) ) : ?>
			<a class="btn btn-dark" href="<?php echo esc_url( jaytech_page_url( 'about-us' ) ); ?>"><?php esc_html_e( 'More About Us', 'jaytech-theme' ); ?></a>
		<?php endif; ?>
	</div>
</section>

<section id="services" class="section section-soft">
	<div class="container">
		<div class="section-heading reveal">
			<span class="eyebrow"><?php esc_html_e( 'What we do', 'jaytech-theme' ); ?></span>
			<h2><?php esc_html_e( 'Our Services', 'jaytech-theme' ); ?></h2>
		</div>
		<div class="grid grid-4">
			<?php foreach ( jaytech_get_services() as $jaytech_service ) : ?>
				<article class="card reveal">
					<span class="card-icon"><?php echo jaytech_icon( $jaytech_service['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?></span>
					<h3><?php echo esc_html( $jaytech_service['title'] ); ?></h3>
					<p><?php echo esc_html( $jaytech_service['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
		<?php if ( jaytech_page_published( 'services' ) ) : ?>
			<p class="section-link"><a class="btn btn-dark" href="<?php echo esc_url( jaytech_page_url( 'services' ) ); ?>"><?php esc_html_e( 'View All Services', 'jaytech-theme' ); ?></a></p>
		<?php endif; ?>
	</div>
</section>

<section id="about" class="section section-dark">
	<div class="container">
		<div class="section-heading reveal">
			<span class="eyebrow"><?php esc_html_e( 'Why JayTech', 'jaytech-theme' ); ?></span>
			<h2><?php esc_html_e( 'A partner you can count on', 'jaytech-theme' ); ?></h2>
		</div>
		<div class="grid grid-4">
			<?php foreach ( $jaytech_reasons as $jaytech_reason ) : ?>
				<div class="reason reveal">
					<span class="reason-icon"><?php echo jaytech_icon( $jaytech_reason[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?></span>
					<h3><?php echo esc_html( $jaytech_reason[1] ); ?></h3>
					<p><?php echo esc_html( $jaytech_reason[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
get_template_part( 'template-parts/cta' );
get_footer();
