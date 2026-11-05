<?php
/**
 * Main fallback template.
 *
 * @package JayTech_Theme
 */

get_header();
?>

<div class="container page-content">
	<?php if ( have_posts() ) : ?>
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1 class="page-title"><?php single_post_title(); ?></h1>
		<?php elseif ( is_archive() ) : ?>
			<h1 class="page-title"><?php the_archive_title(); ?></h1>
		<?php elseif ( is_search() ) : ?>
			<h1 class="page-title">
				<?php
				/* translators: %s: search query. */
				printf( esc_html__( 'Search results for: %s', 'jaytech-theme' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
				?>
			</h1>
		<?php endif; ?>

		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<h2 class="entry-title">
					<?php if ( is_singular() ) : ?>
						<?php the_title(); ?>
					<?php else : ?>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					<?php endif; ?>
				</h2>
				<?php if ( has_post_thumbnail() && ! is_singular() ) : ?>
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large' ); ?></a>
				<?php endif; ?>
				<div class="entry-content">
					<?php is_singular() ? the_content() : the_excerpt(); ?>
				</div>
			</article>
		<?php endwhile; ?>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<h1 class="page-title"><?php esc_html_e( 'Nothing found', 'jaytech-theme' ); ?></h1>
		<p><?php esc_html_e( 'Sorry, no content matched your request.', 'jaytech-theme' ); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
</div>

<?php
get_footer();
