<?php
/**
 * Plugin Name: JayTech Booking
 * Description: Appointment booking system for JayTech IT Solutions.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: JayTech IT Solutions
 * License: GPL-2.0-or-later
 * Text Domain: jaytech-booking
 *
 * @package JayTech_Booking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JB_VERSION', '1.0.0' );
define( 'JB_DIR', plugin_dir_path( __FILE__ ) );
define( 'JB_URL', plugin_dir_url( __FILE__ ) );

require_once JB_DIR . 'includes/class-jb-data.php';
require_once JB_DIR . 'includes/class-jb-post-type.php';
require_once JB_DIR . 'includes/class-jb-admin.php';
require_once JB_DIR . 'includes/class-jb-frontend.php';

JB_Post_Type::init();
JB_Admin::init();
JB_Frontend::init();

register_activation_hook( __FILE__, array( 'JB_Post_Type', 'add_capabilities' ) );
