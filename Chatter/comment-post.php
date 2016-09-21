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
			<form id="new_post" name="new_post" method="post" action="<?php echo site_url(); ?>/">
				<?php if ( 'status' == $post_format || empty( $post_format ) ) : ?>
				<label for="posttext" id="post-prompt">
				<?php Chatter_user_prompt(); ?>
				</label>
				<?php endif; ?>
                                <?php if ( current_user_can( 'upload_files' ) ): ?>
				<div id="media-buttons" class="hide-if-no-js">
				<?php Chatter_media_buttons(); ?>
				</div>				
				<?php endif; ?>
				<div class="inputs">
					<textarea autofocus class="expand70-200" name="posttext" id="posttext" rows="4" cols="60" placeholder="Talk about waste..." ></textarea>
					<label class="post-error" for="posttext" id="posttext_error"></label>
					<div class="postrow">
						<input id="tags" name="tags" type="text" autocomplete="off"
							value="<?php esc_attr_e( 'Tag it', 'Chatter' ); ?>"
							onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Tag it', 'Chatter' ) ); ?>') ? '' : this.value;"
							onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Tag it', 'Chatter' ) ); ?>' : this.value;" />
						<input id="postsubmit" type="submit" value="<?php esc_attr_e( 'Submit!', 'Chatter' ); ?>" />
					</div>
					<input type="hidden" name="post_format" id="post_format" value="<?php echo esc_attr( $post_format ); ?>" />
					<span class="progress spinner-post-new" id="ajaxActivity"></span>
					<?php do_action( 'Chatter_post_form' ); ?>
					<input type="hidden" name="action" value="post" />
					<?php wp_nonce_field( 'new-post' ); ?>
				</div>
			</form>
                        <div class="clear"></div>
