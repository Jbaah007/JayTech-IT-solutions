<?php
/**
 * Inner page header banner.
 *
 * @package JayTech_Theme
 *
 * @var array $args Optional: 'subtitle'.
 */

$jaytech_subtitle = isset( $args['subtitle'] ) ? $args['subtitle'] : '';
?>
<section class="page-hero">
	<div class="container">
		<h1><?php the_title(); ?></h1>
		<?php if ( '' !== $jaytech_subtitle ) : ?>
			<p><?php echo esc_html( $jaytech_subtitle ); ?></p>
		<?php endif; ?>
	</div>
</section>
