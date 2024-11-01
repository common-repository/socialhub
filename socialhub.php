<?php

/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://socialhub.io/
 * @since             1.0.0
 * @package           SocialHub
 *
 * @wordpress-plugin
 * Plugin Name:       SocialHub
 * Description:       Integrates WordPress with SocialHub.
 * Version:           1.0.9
 * Author:            SocialHub Dev Team
 * Author URI:        https://socialhub.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       socialhub
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define('SOCIALHUB_VERSION', '1.0.9');

/**
 * Issuer used for JWT.
 *
 * Ensures these JWT tokens only work with a compatible SocialHub plugin.
 */
define('SOCIALHUB_ISSUER', 'SocialHub');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-socialhub.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_socialhub() {
    $plugin = new SocialHub();
    $plugin->run();
}
run_socialhub();
