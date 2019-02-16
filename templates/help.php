<?php
/**
 * Displays the "System Info" tab.
 *
 * @link       https://mantrabrain.com
 * @since      1.1
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/templates
 */

// Prevent direct access.
if ( ! defined( 'USAR_PATH' ) ) exit;
?>

<h3 id="usar-help-heading"><?php _e( 'Help & Troubleshooting', 'ultimate-search-and-replace' ); ?></h3>

<p><?php printf( __( 'Free support is available on the <a href="%s">plugin support forums</a>.', 'ultimate-search-and-replace' ), 'https://wordpress.org/support/plugin/ultimate-search-and-replace' ); ?></p>


<textarea readonly="readonly" onclick="this.focus(); this.select()" style="width:750px;height:500px;font-family:Menlo,Monaco,monospace; margin-top: 15px;" name='usar-sysinfo'><?php echo USAR_Compatibility::get_sysinfo(); ?></textarea>

<p class="submit">
	<input type="hidden" name="action" value="usar_download_sysinfo" />
	<?php submit_button( __( 'Download System Info', 'ultimate-search-and-replace' ), 'primary', 'usar-download-sysinfo', false ); ?>
</p>
