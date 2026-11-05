<?php
/**
 * Default page template (used by shortcode pages such as Support Tickets).
 *
 * @package JayTech_Theme
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/page-header' );
	?>
	<div class="container page-content">
		<div class="entry-content">
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
