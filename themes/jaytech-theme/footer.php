<?php
/**
 * Site footer.
 *
 * @package JayTech_Theme
 */

?>
</main>

<footer class="site-footer">
	<div class="container footer-grid">
		<div class="footer-col">
			<p class="footer-brand"><?php bloginfo( 'name' ); ?></p>
			<p><?php echo esc_html( jaytech_mod( 'footer_text' ) ); ?></p>
		</div>

		<nav class="footer-col" aria-label="<?php esc_attr_e( 'Footer', 'jaytech-theme' ); ?>">
			<h2 class="footer-title"><?php esc_html_e( 'Quick Links', 'jaytech-theme' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'menu_class'     => 'footer-links',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => 'jaytech_fallback_footer_menu',
				)
			);
			?>
		</nav>

		<div class="footer-col">
			<h2 class="footer-title"><?php esc_html_e( 'Contact', 'jaytech-theme' ); ?></h2>
			<ul class="footer-contact">
				<?php if ( '' !== jaytech_mod( 'phone' ) ) : ?>
					<li><?php echo jaytech_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', jaytech_mod( 'phone' ) ) ); ?>"><?php echo esc_html( jaytech_mod( 'phone' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( '' !== jaytech_mod( 'email' ) ) : ?>
					<li><?php echo jaytech_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?><a href="<?php echo esc_url( 'mailto:' . jaytech_mod( 'email' ) ); ?>"><?php echo esc_html( jaytech_mod( 'email' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( '' !== jaytech_mod( 'address' ) ) : ?>
					<li><?php echo jaytech_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?><span><?php echo nl2br( esc_html( jaytech_mod( 'address' ) ) ); ?></span></li>
				<?php endif; ?>
				<?php if ( '' !== jaytech_mod( 'hours' ) ) : ?>
					<li><?php echo jaytech_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?><span><?php echo esc_html( jaytech_mod( 'hours' ) ); ?></span></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<div class="footer-bottom">
		<div class="container">
			<p>
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
				<?php bloginfo( 'name' ); ?>.
				<?php esc_html_e( 'All rights reserved.', 'jaytech-theme' ); ?>
			</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
