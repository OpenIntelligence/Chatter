<?php
/**
 * Front-end post form.
 *
 * @package Chatter
 */
?>
<script type="text/javascript">
/* <![CDATA[ */
	jQuery(document).ready(function($) {
		$('#post_format').val($('#post-types a.selected').attr('id'));
		$('#post-types a').click(function(e) {
			$('.post-input').hide();
			$('#post-types a').removeClass('selected');
			$(this).addClass('selected');
			if ($(this).attr('id') == 'post') {
				$('#posttitle').val("<?php echo esc_js( __('Post Title', 'Chatter') ); ?>");
			} else {
				$('#posttitle').val('');
			}
			$('#postbox-type-' + $(this).attr('id')).show();
			$('#post_format').val($(this).attr('id'));
			return false;
		});
	});
/* ]]> */
</script>

<?php $post_format = isset( $_GET['p'] ) ? $_GET['p'] : 'status'; ?>
<div id="postbox">
		<ul id="post-types">
			<li><a id="status" class="post-format-button<?php if ( 'status' == $post_format ) : ?> selected<?php endif; ?>" href="<?php echo site_url( '?p=status' ); ?>" title="<?php esc_attr_e( 'Status Update', 'Chatter' ); ?>"><?php _e( 'Status Update', 'Chatter' ); ?></a></li>
			<li><a id="post" class="post-format-button<?php if ( 'post' == $post_format || 'standard' == $post_format ) : ?> selected<?php endif; ?>" href="<?php echo site_url( '?p=post' ); ?>" title="<?php esc_attr_e( 'Blog Post', 'Chatter' ); ?>"><?php _e( 'Blog Post', 'Chatter' ); ?></a></li>
			<li><a id="quote" class="post-format-button<?php if ( 'quote' == $post_format ) : ?> selected<?php endif; ?>" href="<?php echo site_url( '?p=quote' ); ?>" title="<?php esc_attr_e( 'Quote', 'Chatter' ); ?>"><?php _e( 'Quote', 'Chatter' ); ?></a></li>
			<li><a id="link" class="post-format-button<?php if ( 'link' == $post_format ) : ?> selected<?php endif; ?>" href="<?php echo site_url( '?p=link' ); ?>" title="<?php esc_attr_e( 'Link', 'Chatter' ); ?>"><?php _e( 'Link', 'Chatter' ); ?></a></li>
		</ul>
		<div class="avatar">
			<?php echo get_avatar( get_current_user_id(), 48 ); ?>
		</div>
		<div class="inputarea">
			<form id="new_post" name="new_post" method="post" action="<?php echo site_url(); ?>/">
				<?php if ( 'status' == $post_format || empty( $post_format ) ) : ?>
				<label for="posttext" id="post-prompt">
					<?php Chatter_user_prompt(); ?>
				</label>
				<?php endif; ?>
				<div id="postbox-type-post" class="post-input <?php if ( 'post' == $post_format || 'standard' == $post_format ) echo ' selected'; ?>">
					<input type="text" name="posttitle" id="posttitle" value=""
						onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Post Title', 'Chatter' ) ); ?>') ? '' : this.value;"
						onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Post Title', 'Chatter' ) ); ?>' : this.value;" />
				</div>
				<?php if ( current_user_can( 'upload_files' ) ): ?>
				<div id="media-buttons" class="hide-if-no-js">
					<?php Chatter_media_buttons(); ?>
				</div>
				<?php endif; ?>
				<textarea class="expand70-200" name="posttext" id="posttext" rows="4" cols="60"></textarea>
				<div id="postbox-type-quote" class="post-input <?php if ( 'quote' == $post_format ) echo " selected"; ?>">
					<label for="postcitation" class="invisible"><?php _e( 'Citation', 'Chatter' ); ?></label>
						<input id="postcitation" name="postcitation" type="text"
							value="<?php esc_attr_e( 'Citation', 'Chatter' ); ?>"
							onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Citation', 'Chatter' ) ); ?>') ? '' : this.value;"
							onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Citation', 'Chatter' ) ); ?>' : this.value;" />
				</div>
				<label class="post-error" for="posttext" id="posttext_error"></label>
				<div class="postrow">
					<input id="tags" name="tags" type="text" autocomplete="off"
						value="<?php esc_attr_e( 'Tag it', 'Chatter' ); ?>"
						onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Tag it', 'Chatter' ) ); ?>') ? '' : this.value;"
						onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Tag it', 'Chatter' ) ); ?>' : this.value;" />
					<input id="submit" type="submit" value="<?php esc_attr_e( 'Post it', 'Chatter' ); ?>" />
				</div>
				<input type="hidden" name="post_format" id="post_format" value="<?php echo esc_attr( $post_format ); ?>" />
				<span class="progress spinner-post-new" id="ajaxActivity"></span>

				<?php do_action( 'Chatter_post_form' ); ?>

				<input type="hidden" name="action" value="post" />
				<?php wp_nonce_field( 'new-post' ); ?>
			</form>
		</div>
		<div class="clear"></div>
</div> <!-- // postbox -->
