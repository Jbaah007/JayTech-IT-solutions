<?php
/**
 * Customizer settings for editable homepage and contact content.
 *
 * @package JayTech_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Customizer sections and settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function jaytech_customize_register( $wp_customize ) {
	$defaults = jaytech_defaults();

	$wp_customize->add_section(
		'jaytech_home',
		array(
			'title'    => __( 'JayTech Homepage', 'jaytech-theme' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_section(
		'jaytech_contact',
		array(
			'title'       => __( 'JayTech Contact Details', 'jaytech-theme' ),
			'description' => __( 'Shown in the footer and on the Contact page. Replace the placeholders with your real details.', 'jaytech-theme' ),
			'priority'    => 31,
		)
	);

	$fields = array(
		'hero_eyebrow' => array( 'jaytech_home', __( 'Hero label', 'jaytech-theme' ), 'text', 'sanitize_text_field' ),
		'hero_title'   => array( 'jaytech_home', __( 'Hero headline', 'jaytech-theme' ), 'text', 'sanitize_text_field' ),
		'hero_text'    => array( 'jaytech_home', __( 'Hero text', 'jaytech-theme' ), 'textarea', 'sanitize_textarea_field' ),
		'intro_title'  => array( 'jaytech_home', __( 'Introduction heading', 'jaytech-theme' ), 'text', 'sanitize_text_field' ),
		'intro_text'   => array( 'jaytech_home', __( 'Introduction text', 'jaytech-theme' ), 'textarea', 'sanitize_textarea_field' ),
		'footer_text'  => array( 'jaytech_home', __( 'Footer description', 'jaytech-theme' ), 'textarea', 'sanitize_textarea_field' ),
		'phone'        => array( 'jaytech_contact', __( 'Phone', 'jaytech-theme' ), 'text', 'sanitize_text_field' ),
		'email'        => array( 'jaytech_contact', __( 'Email', 'jaytech-theme' ), 'text', 'sanitize_email' ),
		'address'      => array( 'jaytech_contact', __( 'Address', 'jaytech-theme' ), 'textarea', 'sanitize_textarea_field' ),
		'hours'        => array( 'jaytech_contact', __( 'Business hours', 'jaytech-theme' ), 'text', 'sanitize_text_field' ),
	);

	foreach ( $fields as $key => $field ) {
		$wp_customize->add_setting(
			'jaytech_' . $key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => $field[3],
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'jaytech_' . $key,
			array(
				'section' => $field[0],
				'label'   => $field[1],
				'type'    => $field[2],
			)
		);
	}
}
add_action( 'customize_register', 'jaytech_customize_register' );
