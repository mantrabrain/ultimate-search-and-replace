<?php
/**
 * Plugin Name:       Ultimate Search And Replace
 * Description:       Search and replace text from WordPress Database. This plugins allows you to search any string (text) from databsae and replace it with your new text. Mainly while migrating your site this tool helps you to replace text via this plugin.
 * Version:           1.0.1
 * Author:            Mantrabrain
 * Author URI:        https://mantrabrain.com
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       ultimate-search-and-replace
 * Domain Path:       /languages
 * Network:			  true
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// If this file was called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ultimate_search_and_replace() {

	// Allows for overriding the capability required to run the plugin.
	$cap = apply_filters( 'usar_capability', 'install_plugins' );

	// Only load for admins.
	if ( current_user_can( $cap ) ) {

		// Defines the path to the main plugin file.
		define( 'USAR_FILE', __FILE__ );

		// Defines the path to be used for includes.
		define( 'USAR_PATH', plugin_dir_path( USAR_FILE ) );

		// Defines the URL to the plugin.
		define( 'USAR_URL', plugin_dir_url( USAR_FILE ) );

		// Defines the current version of the plugin.
		define( 'USAR_VERSION', '1.0.1' );

		/**
		 * The core plugin class that is used to define internationalization,
		 * dashboard-specific hooks, and public-facing site hooks.
		 */
		require USAR_PATH . 'includes/class-usar-main.php';
		$plugin = new Better_Search_Replace();
		$plugin->run();

	}

}
add_action( 'after_setup_theme', 'run_ultimate_search_and_replace' );
