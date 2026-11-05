<?php
/**
 * Template Name: Services
 *
 * @package JayTech_Theme
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part(
		'template-parts/page-header',
		null,
		array( 'subtitle' => __( 'Technology services for businesses and individuals.', 'jaytech-theme' ) )
	);

	if ( '' !== trim( get_the_content() ) ) :
		?>
		<section class="section section-tight">
			<div class="container narrow">
				<div class="entry-content reveal"><?php the_content(); ?></div>
			</div>
		</section>
		<?php
	endif;
	?>
	<section class="section">
		<div class="container">
			<div class="grid grid-2">
				<?php foreach ( jaytech_get_services() as $jaytech_service ) : ?>
					<article class="card service-detail reveal">
						<span class="card-icon"><?php echo jaytech_icon( $jaytech_service['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?></span>
						<h2><?php echo esc_html( $jaytech_service['title'] ); ?></h2>
						<p><?php echo esc_html( $jaytech_service['text'] ); ?></p>
						<ul class="check-list">
							<?php foreach ( $jaytech_service['points'] as $jaytech_point ) : ?>
								<li><?php echo jaytech_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?><?php echo esc_html( $jaytech_point ); ?></li>
							<?php endforeach; ?>
						</ul>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/cta' );
endwhile;

get_footer();
