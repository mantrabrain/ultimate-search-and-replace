<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Registers styles and scripts, adds the custom administration page,
 * and processes user input on the "search/replace" form.
 *
 * @link       https://mantrabrain.com
 * @since      1.0.0
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/includes
 */

// Prevent direct access.
if ( ! defined( 'USAR_PATH' ) ) exit;

class USAR_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ultimate_search_and_replace    The ID of this plugin.
	 */
	private $ultimate_search_and_replace;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $ultimate_search_and_replace       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $ultimate_search_and_replace, $version ) {
		$this->ultimate_search_and_replace = $ultimate_search_and_replace;
		$this->version = $version;
	}

	/**
	 * Register any CSS and JS used by the plugin.
	 * @since    1.0.0
	 * @access 	 public
	 * @param    string $hook Used for determining which page(s) to load our scripts.
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'tools_page_ultimate-search-and-replace' === $hook ) {
			wp_enqueue_style( 'ultimate-search-and-replace', USAR_URL . 'assets/css/ultimate-search-and-replace.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'jquery-style', USAR_URL . 'assets/css/jquery-ui.min.css', array(), $this->version, 'all' );
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script( 'ultimate-search-and-replace', USAR_URL . 'assets/js/ultimate-search-and-replace.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );

			wp_localize_script( 'ultimate-search-and-replace', 'usar_object_vars', array(
				'page_size' 	=> get_option( 'usar_page_size' ) ? absint( get_option( 'usar_page_size' ) ) : 20000,
				'endpoint' 		=> USAR_AJAX::get_endpoint(),
				'ajax_nonce' 	=> wp_create_nonce( 'usar_ajax_nonce' ),
				'no_search' 	=> __( 'No search string was defined, please enter a URL or string to search for.', 'ultimate-search-and-replace' ),
				'no_tables' 	=> __( 'Please select the tables that you want to update.', 'ultimate-search-and-replace' ),
				'unknown' 		=> __( 'An error occurred processing your request. Try decreasing the "Max Page Size", or contact support.', 'ultimate-search-and-replace' ),
				'processing'	=> __( 'Processing...', 'ultimate-search-and-replace' )
			) );
		}
	}

	/**
	 * Register any menu pages used by the plugin.
	 * @since  1.0.0
	 * @access public
	 */
	public function usar_menu_pages() {
		$cap = apply_filters( 'usar_capability', 'install_plugins' );
		add_submenu_page( 'tools.php', __( 'Ultimate Search And Replace', 'ultimate-search-and-replace' ), __( 'Ultimate Search And Replace', 'ultimate-search-and-replace' ), $cap, 'ultimate-search-and-replace', array( $this, 'usar_menu_pages_callback' ) );
	}

	/**
	 * The callback for creating a new submenu page under the "Tools" menu.
	 * @access public
	 */
	public function usar_menu_pages_callback() {
		require_once USAR_PATH . 'templates/dashboard.php';
	}

	/**
	 * Renders the result or error onto the ultimate-search-and-replace admin page.
	 * @access public
	 */
	public static function render_result() {

		if ( isset( $_GET['result'] ) && $result = get_transient( 'usar_results' ) ) {

			if ( isset( $result['dry_run'] ) && $result['dry_run'] === 'on' ) {
				$msg = sprintf( __( '<p><strong>DRY RUN:</strong> <strong>%d</strong> tables were searched, <strong>%d</strong> cells were found that need to be updated, and <strong>%d</strong> changes were made.</p><p><a href="%s" class="thickbox" title="Dry Run Details">Click here</a> for more details, or use the form below to run the search/replace.</p>', 'ultimate-search-and-replace' ),
					$result['tables'],
					$result['change'],
					$result['updates'],
					get_admin_url() . 'admin-post.php?action=usar_view_details&TB_iframe=true&width=800&height=500'
				);
			} else {
				$msg = sprintf( __( '<p>During the search/replace, <strong>%d</strong> tables were searched, with <strong>%d</strong> cells changed in <strong>%d</strong> updates.</p><p><a href="%s" class="thickbox" title="Search/Replace Details">Click here</a> for more details.</p>', 'ultimate-search-and-replace' ),
					$result['tables'],
					$result['change'],
					$result['updates'],
					get_admin_url() . 'admin-post.php?action=usar_view_details&TB_iframe=true&width=800&height=500'
				);
			}

			echo '<div class="updated">' . $msg . '</div>';

		}

	}

	/**
	 * Prefills the given value on the search/replace page (dry run, live run, from profile).
	 * @access public
	 * @param  string $value The value to check for.
	 * @param  string $type  The type of the value we're filling.
	 */
	public static function prefill_value( $value, $type = 'text' ) {

		// Grab the correct data to prefill.
		if ( isset( $_GET['result'] ) && get_transient( 'usar_results' ) ) {
			$values = get_transient( 'usar_results' );
		} else {
			$values = array();
		}

		// Prefill the value.
		if ( isset( $values[$value] ) ) {

			if ( 'checkbox' === $type && 'on' === $values[$value] ) {
				echo 'checked';
			} else {
				echo str_replace( '#USAR_BACKSLASH#', '\\', esc_attr( $values[$value] ) );
			}

		}

	}

	/**
	 * Loads the tables available to run a search replace, prefilling if already
	 * selected the tables.
	 * @access public
	 */
	public static function load_tables() {

		// Get the tables and their sizes.
		$tables 	= USAR_DB::get_tables();
		$sizes 		= USAR_DB::get_sizes();

		echo '<select id="usar-table-select" name="select_tables[]" multiple="multiple" style="width:25em;">';

		foreach ( $tables as $table ) {

			// Try to get the size for this specific table.
			$table_size = isset( $sizes[$table] ) ? $sizes[$table] : '';

			if ( isset( $_GET['result'] ) && get_transient( 'usar_results' ) ) {

				$result = get_transient( 'usar_results' );

				if ( isset( $result['table_reports'][$table] ) ) {
					echo "<option value='$table' selected>$table $table_size</option>";
				} else {
					echo "<option value='$table'>$table $table_size</option>";
				}

			} else {
				echo "<option value='$table'>$table $table_size</option>";
			}

		}

		echo '</select>';

	}

	/**
	 * Loads the result details (via Thickbox).
	 * @access public
	 */
	public function load_details() {

		if ( get_transient( 'usar_results' ) ) {

			$results 		= get_transient( 'usar_results' );
			$min 			= ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			$usar_styles 	= USAR_URL . 'assets/css/ultimate-search-and-replace.css?v=' . USAR_VERSION;

			?>
			<link href="<?php echo esc_url( get_admin_url( null, 'css/common' . $min . '.css' ) ); ?>" rel="stylesheet" type="text/css" />
			<link href="<?php echo esc_url( $usar_styles ); ?>" rel="stylesheet" type="text/css">

			<div class="container" style="padding:10px;">

				<table id="usar-results-table" class="widefat">
					<thead>
						<tr><th class="usar-first"><?php _e( 'Table', 'ultimate-search-and-replace' ); ?></th><th class="usar-second"><?php _e( 'Changes Found', 'ultimate-search-and-replace' ); ?></th><th class="usar-third"><?php _e( 'Rows Updated', 'ultimate-search-and-replace' ); ?></th><th class="usar-fourth"><?php _e( 'Time', 'ultimate-search-and-replace' ); ?></th></tr>
					</thead>
					<tbody>
					<?php
						foreach ( $results['table_reports'] as $table_name => $report ) {
							$time = $report['end'] - $report['start'];

							if ( $report['change'] !== 0 ) {
								$report['change'] = '<strong>' . $report['change'] . '</strong>';
							}

							if ( $report['updates'] !== 0 ) {
								$report['updates'] = '<strong>' . $report['updates'] . '</strong>';
							}

							printf(
								'<tr><td class="usar-first">%s</td><td class="usar-second">%s</td><td class="usar-third">%s</td><td class="usar-fourth">%s %s</td></tr>',
								$table_name,
								$report['change'],
								$report['updates'],
								round( $time, 3 ),
								__( 'seconds', 'ultimate-search-and-replace' )
							);

						}
					?>
					</tbody>
				</table>


			</div>
			<?php
		}
	}

	/**
	 * Registers our settings in the options table.
	 * @access public
	 */
	public function register_option() {
		register_setting( 'usar_settings_fields', 'usar_page_size', 'absint' );
	}

	/**
	 * Downloads the system info file for support.
	 * @access public
	 */
	public function download_sysinfo() {
		$cap = apply_filters( 'usar_capability', 'install_plugins' );
		if ( ! current_user_can( $cap ) ) {
			return;
		}

		nocache_headers();

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="usar-system-info.txt"' );

		echo wp_strip_all_tags( $_POST['usar-sysinfo'] );
		die();
	}

	/**
	 * Displays the link to upgrade to USAR Pro
	 * @access public
	 * @param array $links The links assigned to the plugin.
	 */
	public function meta_upgrade_link( $links, $file ) {
		$plugin = plugin_basename( USAR_FILE );

		if ( $file == $plugin ) {
			return array_merge(
				$links,
				array( '<a href="https://mantrabrain.com/?utm_source=insideplugin&utm_medium=web&utm_content=plugins-page&utm_campaign=pro-upsell">' . __( 'Upgrade to Pro', 'ultimate-search-and-replace' ) . '</a>' )
			);
		}

  		return $links;
	}

}
