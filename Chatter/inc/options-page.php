<?php
/**
 * Theme options page.
 *
 * @package Chatter
 * @since Chatter 0.1
 */

add_action( 'admin_menu', array( 'Chatter_Options', 'init' ) );

class Chatter_Options {

	static function init() {
		global $plugin_page;

		add_theme_page( __( 'Theme Options', 'Chatter' ), __( 'Theme Options', 'Chatter' ), 'edit_theme_options', 'Chatter-options-page', array( 'Chatter_Options', 'page' ) );

		if ( 'Chatter-options-page' == $plugin_page ) {
			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'colorpicker' );
			wp_enqueue_style( 'colorpicker' );
		}
	}

	static function page() {
		register_setting( 'Chatterops', 'prologue_show_titles' );
		register_setting( 'Chatterops', 'Chatter_allow_users_publish' );
		register_setting( 'Chatterops', 'Chatter_prompt_text' );
		register_setting( 'Chatterops', 'Chatter_background_color' );
		register_setting( 'Chatterops', 'Chatter_background_image' );
		register_setting( 'Chatterops', 'Chatter_hide_threads' );

		$prologue_show_titles_val    = get_option( 'prologue_show_titles' );
		$Chatter_allow_users_publish_val  = get_option( 'Chatter_allow_users_publish' );
		$Chatter_prompt_text_val          = get_option( 'Chatter_prompt_text' );
		$Chatter_background_color         = get_option( 'Chatter_background_color' );
		$Chatter_background_image         = get_option( 'Chatter_background_image' );
		$Chatter_hide_threads             = get_option( 'Chatter_hide_threads' );

		if ( isset( $_POST[ 'action' ] ) && esc_attr( $_POST[ 'action' ] ) == 'update' ) {
			check_admin_referer( 'Chatterops-options' );

			if ( isset( $_POST[ 'prologue_show_titles' ] ) )
				$prologue_show_titles_val = intval( $_POST[ 'prologue_show_titles' ] );
			else
				$prologue_show_titles_val = 0;

			if ( isset( $_POST[ 'Chatter_allow_users_publish' ] ) )
				$Chatter_allow_users_publish_val = intval( $_POST[ 'Chatter_allow_users_publish' ] );
			else
				$Chatter_allow_users_publish_val = 0;

			if ( isset( $_POST[ 'Chatter_background_color_hex' ] ) ) {
				// color value must be 3 or 6 hexadecimal characters
				if ( preg_match( '/^#?([a-f0-9]{3}){1,2}$/i', $_POST['Chatter_background_color_hex'] ) ) {
					$Chatter_background_color = $_POST['Chatter_background_color_hex'];
					// if color value doesn't have a preceding hash, add it
					if ( false === strpos( $Chatter_background_color, '#' ) )
						$Chatter_background_color = '#' . $Chatter_background_color;
				} else {
					$Chatter_background_color = '';
				}
			}

			if ( esc_attr( $_POST[ 'Chatter_prompt_text' ] ) != __( "Whatcha' up to?", 'Chatter') )
				$Chatter_prompt_text_val = esc_attr( $_POST[ 'Chatter_prompt_text' ] );

			if ( isset( $_POST[ 'Chatter_hide_threads' ] ) )
				$Chatter_hide_threads = $_POST[ 'Chatter_hide_threads' ];
			else
				$Chatter_hide_threads = false;

			if ( isset( $_POST['Chatter_background_image'] ) )
				$Chatter_background_image = $_POST[ 'Chatter_background_image' ];
			else
				$Chatter_background_image = 'none';

			update_option( 'prologue_show_titles', $prologue_show_titles_val );
			update_option( 'Chatter_allow_users_publish', $Chatter_allow_users_publish_val );
			update_option( 'Chatter_prompt_text', $Chatter_prompt_text_val );
			update_option( 'Chatter_background_color', $Chatter_background_color );
			update_option( 'Chatter_background_image', $Chatter_background_image );
			update_option( 'Chatter_hide_threads', $Chatter_hide_threads );

		?>
			<div class="updated"><p><strong><?php _e( 'Options saved.', 'Chatter' ); ?></strong></p></div>
		<?php

			} ?>

		<div class="wrap">
	    <?php echo "<h2>" . __( 'Chatter Options', 'Chatter' ) . "</h2>"; ?>

		<form enctype="multipart/form-data" name="form1" method="post" action="<?php echo esc_attr( str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ) ); ?>">

			<h3 style="font-family: georgia, times, serif; font-weight: normal; border-bottom: 1px solid #ddd; padding-bottom: 5px">
				<?php _e( 'Functionality Options', 'Chatter' ); ?>
			</h3>

			<?php settings_fields( 'Chatterops' ); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e( 'Posting Access:', 'Chatter' ); ?></th>
						<td>

						<input id="Chatter_allow_users_publish" type="checkbox" name="Chatter_allow_users_publish" <?php if ( $Chatter_allow_users_publish_val == 1 ) echo 'checked="checked"'; ?> value="1" />

						<?php if ( defined( 'IS_WPCOM' ) && IS_WPCOM )
								$msg = __( 'Allow any WordPress.com member to post', 'Chatter' );
							  else
							  	$msg = __( 'Allow any registered member to post', 'Chatter' );
						 ?>

						<label for="Chatter_allow_users_publish"><?php echo $msg; ?></label>

						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Hide Threads:', 'Chatter' ); ?></th>
						<td>

						<input id="Chatter_hide_threads" type="checkbox" name="Chatter_hide_threads" <?php if ( $Chatter_hide_threads == 1 ) echo 'checked="checked"'; ?> value="1" />
						<label for="Chatter_hide_threads"><?php _e( 'Hide comment threads by default on all non-single views', 'Chatter' ); ?></label>

						</td>
					</tr>
				</tbody>
			</table>

			<p>&nbsp;</p>

			<h3 style="font-family: georgia, times, serif; font-weight: normal; border-bottom: 1px solid #ddd; padding-bottom: 5px">
				<?php _e( 'Design Options', 'Chatter' ); ?>
			</h3>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e( 'Custom Background Color:', 'Chatter' ); ?></th>
						<td>
							<input id="pickcolor" type="button" class="button" name="pickcolor" value="<?php esc_attr_e( 'Pick a Color', 'Chatter' ); ?> "/>
							<input name="Chatter_background_color_hex" id="Chatter_background_color_hex" type="text" value="<?php echo esc_attr( $Chatter_background_color ); ?>" />
							<div id="colorPickerDiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"> </div>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Background Image:', 'Chatter' ); ?></th>
						<td>
							<input type="radio" id="bi_none" name="Chatter_background_image" value="none"<?php if ( 'none' == $Chatter_background_image ) : ?> checked="checked" <?php endif; ?>/> <label for="bi_none"><?php _e( 'None', 'Chatter' ); ?></label><br />
							<input type="radio" id="bi_bubbles" name="Chatter_background_image" value="bubbles"<?php if ( 'bubbles' == $Chatter_background_image ) : ?> checked="checked" <?php endif; ?>/> <label for="bi_bubbles"><?php _e( 'Bubbles', 'Chatter' ); ?></label><br />
							<input type="radio" id="bi_polka" name="Chatter_background_image" value="dots"<?php if ( 'dots' == $Chatter_background_image ) : ?> checked="checked" <?php endif; ?>/> <label for="bi_polka"><?php _e( 'Polka Dots', 'Chatter' ); ?></label><br />
							<input type="radio" id="bi_squares" name="Chatter_background_image" value="squares"<?php if ( 'squares' == $Chatter_background_image ) : ?> checked="checked" <?php endif; ?>/> <label for="bi_squares"><?php _e( 'Squares', 'Chatter' ); ?></label><br />
							<input type="radio" id="bi_plaid" name="Chatter_background_image" value="plaid"<?php if ( 'plaid' == $Chatter_background_image ) : ?> checked="checked" <?php endif; ?>/> <label for="bi_plaid"><?php _e( 'Plaid', 'Chatter' ); ?></label><br />
							<input type="radio" id="bi_stripes" name="Chatter_background_image" value="stripes"<?php if ( 'stripes' == $Chatter_background_image ) : ?> checked="checked" <?php endif; ?>/> <label for="bi_stripes"><?php _e( 'Stripes', 'Chatter' ); ?></label><br />
							<input type="radio" id="bi_santa" name="Chatter_background_image" value="santa"<?php if ( 'santa' == $Chatter_background_image ) : ?> checked="checked" <?php endif; ?>/> <label for="bi_santa"><?php _e( 'Santa', 'Chatter' ); ?></label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Post prompt:', 'Chatter' ); ?></th>
						<td>
							<input id="Chatter_prompt_text" type="input" name="Chatter_prompt_text" value="<?php echo ($Chatter_prompt_text_val == __( "Whatcha' up to?", 'Chatter') ) ? __("Whatcha' up to?", 'Chatter') : stripslashes( $Chatter_prompt_text_val ); ?>" />
				 			(<?php _e( 'if empty, defaults to <strong>Whatcha up to?</strong>', 'Chatter' ); ?>)
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Post Titles:', 'Chatter' )?></th>
						<td>
							<input id="prologue_show_titles" type="checkbox" name="prologue_show_titles" <?php if ( $prologue_show_titles_val != 0 ) echo 'checked="checked"'; ?> value="1" />
							<label for="prologue_show_titles"><?php _e( 'Display titles', 'Chatter' ); ?></label>
						</td>
					</tr>
				</tbody>
			</table>

			<p>
			</p>

			<p class="submit">
				<input type="submit" name="Submit" value="<?php esc_attr_e( 'Update Options', 'Chatter' ); ?>" />
			</p>

		</form>
		</div>

<script type="text/javascript">
/* <![CDATA[ */
	var farbtastic;

	function pickColor(color) {
		jQuery('#Chatter_background_color_hex').val(color);
		farbtastic.setColor(color);
	}

	jQuery(document).ready(function() {
		jQuery('#pickcolor').click(function() {
			jQuery('#colorPickerDiv').show();
		});

		jQuery('#hidetext' ).click(function() {
			toggle_text();
		});

		farbtastic = jQuery.farbtastic( '#colorPickerDiv', function(color) { pickColor(color); });
	});

	jQuery(document).mousedown(function(){
		// Make the picker disappear, since we're using it in an independant div
		hide_picker();
	});

	function colorDefault() {
		pickColor( '#<?php echo HEADER_TEXTCOLOR; ?>' );
	}

	function hide_picker(what) {
		var update = false;
		jQuery('#colorPickerDiv').each(function(){
			var id = jQuery(this).attr( 'id' );
			if (id == what) {
				return;
			}
			var display = jQuery(this).css( 'display' );
			if (display == 'block' ) {
				jQuery(this).fadeOut(2);
			}
		});
	}

	function toggle_text(force) {
		if (jQuery('#textcolor').val() == 'blank' ) {
			//Show text
			jQuery(buttons.toString()).show();
			jQuery('#textcolor').val( '<?php echo HEADER_TEXTCOLOR; ?>' );
			jQuery('#hidetext').val( '<?php _e( 'Hide Text', 'Chatter' ); ?>' );
		}
		else {
			//Hide text
			jQuery(buttons.toString()).hide();
			jQuery('#textcolor').val( 'blank' );
			jQuery('#hidetext').val( '<?php _e( 'Show Text', 'Chatter' ); ?>' );
		}
	}
/* ]]> */
</script>

<?php
	}
}
