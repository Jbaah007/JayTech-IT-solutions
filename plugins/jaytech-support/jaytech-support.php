<?php
/**
 * Plugin Name: JayTech Support
 * Description: Customer support ticket system for JayTech IT Solutions.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: JayTech IT Solutions
 * License: GPL-2.0-or-later
 * Text Domain: jaytech-support
 *
 * @package JayTech_Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JT_SUPPORT_VERSION', '1.0.0' );
define( 'JT_SUPPORT_FILE', __FILE__ );
define( 'JT_SUPPORT_DIR', plugin_dir_path( __FILE__ ) );
define( 'JT_SUPPORT_URL', plugin_dir_url( __FILE__ ) );

require_once JT_SUPPORT_DIR . 'includes/class-jt-data.php';
require_once JT_SUPPORT_DIR . 'includes/class-jt-post-type.php';
require_once JT_SUPPORT_DIR . 'includes/class-jt-admin.php';
require_once JT_SUPPORT_DIR . 'includes/class-jt-frontend.php';
require_once JT_SUPPORT_DIR . 'includes/class-jt-dashboard.php';

JT_Post_Type::init();
JT_Admin::init();
JT_Frontend::init();
JT_Dashboard::init();

register_activation_hook( __FILE__, array( 'JT_Post_Type', 'add_capabilities' ) );
