<?php

/**
 * Displays the main Ultimate Search And Replace page under Tools -> Ultimate Search And Replace.
 *
 * @link       https://mantrabrain.com
 * @since      1.0.0
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/templates
 */

// Prevent direct access.
if ( ! defined( 'USAR_PATH' ) ) exit;

// Determines which tab to display.
$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'usar_search_replace';

switch( $active_tab ) {
	case 'usar_settings':
		$action = 'action="' . get_admin_url() . 'options.php' . '"';
		break;
	case 'usar_help':
		$action = 'action="' . get_admin_url() . 'admin-post.php' . '"';
		break;
	default:
		$action = '';
}

?>

<div class="wrap">

	<h1 id="usar-title"><?php _e( 'Ultimate Search And Replace', 'ultimate-search-and-replace' ); ?></h1>
	<?php settings_errors(); ?>

	<div id="usar-error-wrap"></div>

	<?php USAR_Admin::render_result(); ?>

	<div id="usar-main">

		<div id="usar-tabs">

			<h2 id="usar-nav-tab-wrapper" class="nav-tab-wrapper">
			    <a href="?page=ultimate-search-and-replace&tab=usar_search_replace" class="nav-tab <?php echo $active_tab == 'usar_search_replace' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Search/Replace', 'ultimate-search-and-replace' ); ?></a>
			    <a href="?page=ultimate-search-and-replace&tab=usar_settings" class="nav-tab <?php echo $active_tab == 'usar_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings', 'ultimate-search-and-replace' ); ?></a>
			    <a href="?page=ultimate-search-and-replace&tab=usar_help" class="nav-tab <?php echo $active_tab == 'usar_help' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Help', 'ultimate-search-and-replace' ); ?></a>
			</h2>

			<form class="usar-action-form" <?php echo $action; ?> method="POST">

			<?php
				// Include the correct tab template.
				$usar_template = str_replace( '_', '-', str_replace('usar_','',$active_tab )) . '.php';
				if ( file_exists( USAR_PATH . 'templates/' . $usar_template ) ) {
					include USAR_PATH . 'templates/' . $usar_template;
				} else {
					include USAR_PATH . 'templates/search-replace.php';
				}
			?>

			</form>

		</div><!-- /#usar-tabs -->


	</div><!-- /#usar-main -->

</div><!-- /.wrap -->
