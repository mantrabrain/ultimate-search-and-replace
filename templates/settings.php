<?php
/**
 * Displays the main "Settings" tab.
 *
 * @link       https://mantrabrain.com
 * @since      1.1
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/templates
 */

// Prevent direct/unauthorized access.
if ( ! defined( 'USAR_PATH' ) ) exit;

// Other settings.
$page_size 	= get_option( 'usar_page_size' ) ? absint( get_option( 'usar_page_size' ) ) : 20000;

 ?>

<?php settings_fields( 'usar_settings_fields' ); ?>

<table class="form-table">
	<tbody>

		<tr valign="top">
			<th scope="row" valign="top">
				<?php _e( 'Max Page Size', 'ultimate-search-and-replace' ); ?>
			</th>
			<td>
				<div id="usar-page-size-slider" class="usar-slider"></div>
				<br><span id="usar-page-size-info"><?php _e( 'Current Setting: ', 'ultimate-search-and-replace' ); ?></span><span id="usar-page-size-value"><?php echo $page_size; ?></span>
				<input id="usar_page_size" type="hidden" name="usar_page_size" value="<?php echo $page_size; ?>" />
				<p class="description"><?php _e( 'If you\'re noticing timeouts or getting a white screen while running a search replace, try decreasing this value.', 'ultimate-search-and-replace' ); ?></p>

			</td>
		</tr>

	</tbody>
</table>
<?php submit_button(); ?>
