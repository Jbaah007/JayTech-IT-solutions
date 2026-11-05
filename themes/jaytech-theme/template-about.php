<?php
/**
 * Template Name: About Us
 *
 * @package JayTech_Theme
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part(
		'template-parts/page-header',
		null,
		array( 'subtitle' => __( 'Your dedicated technology partner.', 'jaytech-theme' ) )
	);
	?>
	<div class="about-page entry-content">
		<?php
		$jaytech_about_content = get_the_content();
		if ( '' === trim( $jaytech_about_content ) ) {
			// Empty page: show the built-in default so the page is never blank.
			echo apply_filters( 'the_content', jaytech_about_default_content() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Theme-generated, escaped markup.
		} elseif ( false !== strpos( $jaytech_about_content, 'jt-section' ) ) {
			the_content();
		} else {
			// Plain editor content: keep it inside the page container.
			echo '<div class="container page-content">';
			the_content();
			echo '</div>';
		}
		?>
	</div>
	<?php
endwhile;

get_footer();
