<?php
/**
 * Default About Us content (block markup) and one-time seeding of the existing page.
 *
 * @package JayTech_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Heading block.
 *
 * @param string $text  Text.
 * @param int    $level Heading level.
 * @return string
 */
function jaytech_blk_heading( $text, $level = 2 ) {
	$attrs = 2 === $level ? '' : ' {"level":' . (int) $level . '}';
	return '<!-- wp:heading' . $attrs . " -->\n<h" . (int) $level . ' class="wp-block-heading">' . esc_html( $text ) . '</h' . (int) $level . ">\n<!-- /wp:heading -->\n\n";
}

/**
 * Paragraph block.
 *
 * @param string $text  Text.
 * @param string $class Optional CSS class.
 * @return string
 */
function jaytech_blk_paragraph( $text, $class = '' ) {
	$attrs = $class ? ' {"className":"' . $class . '"}' : '';
	$attr  = $class ? ' class="' . $class . '"' : '';
	return '<!-- wp:paragraph' . $attrs . " -->\n<p" . $attr . '>' . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
}

/**
 * Group block.
 *
 * @param string $inner Inner block markup.
 * @param string $class CSS class.
 * @return string
 */
function jaytech_blk_group( $inner, $class ) {
	return '<!-- wp:group {"className":"' . $class . "\"} -->\n<div class=\"wp-block-group " . $class . '">' . "\n" . $inner . "</div>\n<!-- /wp:group -->\n\n";
}

/**
 * Columns block from an array of inner-block markup strings.
 *
 * @param array $columns Column contents.
 * @return string
 */
function jaytech_blk_columns( $columns ) {
	$out = "<!-- wp:columns -->\n<div class=\"wp-block-columns\">\n";
	foreach ( $columns as $column ) {
		$out .= "<!-- wp:column -->\n<div class=\"wp-block-column\">\n" . $column . "</div>\n<!-- /wp:column -->\n\n";
	}
	return $out . "</div>\n<!-- /wp:columns -->\n\n";
}

/**
 * A titled card (group with heading and text).
 *
 * @param string $title Title.
 * @param string $text  Text.
 * @return string
 */
function jaytech_blk_card( $title, $text ) {
	return jaytech_blk_group( jaytech_blk_heading( $title, 3 ) . jaytech_blk_paragraph( $text ), 'jt-card' );
}

/**
 * List block.
 *
 * @param array $items Items.
 * @return string
 */
function jaytech_blk_list( $items ) {
	$out = "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( $items as $item ) {
		$out .= "<!-- wp:list-item -->\n<li>" . esc_html( $item ) . "</li>\n<!-- /wp:list-item -->";
	}
	return $out . "</ul>\n<!-- /wp:list -->\n\n";
}

/**
 * Button block.
 *
 * @param string $label Label.
 * @param string $url   URL.
 * @param string $class Optional class (e.g. is-style-outline).
 * @return string
 */
function jaytech_blk_button( $label, $url, $class = '' ) {
	$attrs = $class ? ' {"className":"' . $class . '"}' : '';
	$cls   = 'wp-block-button' . ( $class ? ' ' . $class : '' );
	return '<!-- wp:button' . $attrs . " -->\n<div class=\"" . $cls . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '">' . esc_html( $label ) . "</a></div>\n<!-- /wp:button -->\n\n";
}

/**
 * Default About Us page content as block markup.
 *
 * @return string
 */
function jaytech_about_default_content() {
	$out = '';

	$out .= jaytech_blk_group(
		jaytech_blk_paragraph( __( 'About Us', 'jaytech-theme' ), 'eyebrow' )
		. jaytech_blk_heading( __( 'About JayTech IT Solutions', 'jaytech-theme' ) )
		. jaytech_blk_paragraph( __( 'JayTech IT Solutions is an IT services provider built around a simple idea: technology should help you get things done, not get in the way. We offer technical support, cloud solutions, networking, and web development for people and businesses that want dependable IT without the runaround.', 'jaytech-theme' ) )
		. jaytech_blk_paragraph( __( 'Whether you need a quick fix for a stubborn problem or a long-term plan for your systems, we bring the same practical approach to every job.', 'jaytech-theme' ) ),
		'jt-section jt-intro'
	);

	$out .= jaytech_blk_group(
		jaytech_blk_heading( __( 'Who We Are', 'jaytech-theme' ) )
		. jaytech_blk_columns(
			array(
				jaytech_blk_paragraph( __( 'We are a team focused on helping individuals and businesses solve everyday technology problems. Some clients come to us with a single issue that needs fixing today. Others want a trusted partner to look after their systems over time.', 'jaytech-theme' ) )
				. jaytech_blk_paragraph( __( 'Either way, we take the time to understand what you are trying to achieve, explain your options in plain language, and recommend what makes sense for your situation and budget.', 'jaytech-theme' ) ),
				jaytech_blk_group(
					jaytech_blk_heading( __( 'Who we help', 'jaytech-theme' ), 3 )
					. jaytech_blk_list(
						array(
							__( 'Individuals and home users', 'jaytech-theme' ),
							__( 'Small businesses', 'jaytech-theme' ),
							__( 'Growing teams that need reliable IT', 'jaytech-theme' ),
						)
					),
					'jt-card'
				),
			)
		),
		'jt-section jt-section--soft'
	);

	$out .= jaytech_blk_group(
		jaytech_blk_heading( __( 'Our Mission', 'jaytech-theme' ) )
		. jaytech_blk_paragraph( __( 'To provide reliable, practical, and accessible IT solutions that help our customers work with confidence.', 'jaytech-theme' ), 'jt-lead' )
		. jaytech_blk_columns(
			array(
				jaytech_blk_card( __( 'Reliable', 'jaytech-theme' ), __( 'Solutions you can depend on, with clear follow-through from start to finish.', 'jaytech-theme' ) ),
				jaytech_blk_card( __( 'Practical', 'jaytech-theme' ), __( 'Advice and fixes that fit how you actually work, without unnecessary extras.', 'jaytech-theme' ) ),
				jaytech_blk_card( __( 'Accessible', 'jaytech-theme' ), __( 'Friendly, jargon-free help that makes technology approachable for everyone.', 'jaytech-theme' ) ),
			)
		),
		'jt-section jt-section--dark'
	);

	$out .= jaytech_blk_group(
		jaytech_blk_heading( __( 'What We Do', 'jaytech-theme' ) )
		. jaytech_blk_columns(
			array(
				jaytech_blk_card( __( 'Technical Support', 'jaytech-theme' ), __( 'Troubleshooting, setup, and ongoing help for devices, software, and accounts.', 'jaytech-theme' ) ),
				jaytech_blk_card( __( 'Cloud Solutions', 'jaytech-theme' ), __( 'Planning, migration, and backup solutions that keep your data safe and within reach.', 'jaytech-theme' ) ),
				jaytech_blk_card( __( 'Networking', 'jaytech-theme' ), __( 'Dependable wired and wireless networks, configured securely and easy to manage.', 'jaytech-theme' ) ),
				jaytech_blk_card( __( 'Web Development', 'jaytech-theme' ), __( 'Fast, mobile-friendly websites and WordPress solutions that present your business well.', 'jaytech-theme' ) ),
			)
		),
		'jt-section'
	);

	$out .= jaytech_blk_group(
		jaytech_blk_heading( __( 'Why Choose Us', 'jaytech-theme' ) )
		. jaytech_blk_columns(
			array(
				jaytech_blk_card( __( 'Technical Expertise', 'jaytech-theme' ), __( 'Hands-on knowledge across support, cloud, networking, and web, so one partner can cover your needs.', 'jaytech-theme' ) ),
				jaytech_blk_card( __( 'Responsive Support', 'jaytech-theme' ), __( 'We keep you informed and reply promptly, so problems do not sit unanswered.', 'jaytech-theme' ) ),
				jaytech_blk_card( __( 'Customer-Focused Service', 'jaytech-theme' ), __( 'Your goals come first. We listen, explain clearly, and tailor our work to you.', 'jaytech-theme' ) ),
			)
		),
		'jt-section jt-section--soft'
	);

	$out .= jaytech_blk_group(
		jaytech_blk_heading( __( 'Ready to talk?', 'jaytech-theme' ) )
		. jaytech_blk_paragraph( __( 'Tell us what you need or book a time that suits you. We would be glad to help.', 'jaytech-theme' ) )
		. "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\">\n"
		. jaytech_blk_button( __( 'Contact Us', 'jaytech-theme' ), jaytech_page_url( 'contact' ) )
		. jaytech_blk_button( __( 'Book Appointment', 'jaytech-theme' ), jaytech_page_url( 'book-appointment' ), 'is-style-outline' )
		. "</div>\n<!-- /wp:buttons -->\n\n",
		'jt-section jt-section--cta'
	);

	return $out;
}

/**
 * Fill the existing, empty About Us page once so it is editable in the page editor.
 * Never overwrites a page that already has content.
 */
function jaytech_seed_about_page() {
	if ( get_option( 'jaytech_about_seeded' ) ) {
		return;
	}

	$page = get_page_by_path( 'about-us' );
	if ( ! $page || ! current_user_can( 'edit_post', $page->ID ) ) {
		return;
	}

	if ( '' === trim( $page->post_content ) ) {
		wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_content' => wp_slash( jaytech_about_default_content() ),
			)
		);
	}

	update_option( 'jaytech_about_seeded', 1 );
}
add_action( 'admin_init', 'jaytech_seed_about_page' );
