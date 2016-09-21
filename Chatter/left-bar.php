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
<div class="postboxcontent">
    <div id="postbox">
        <div id="side-avatar">
        <?php if ( Chatter_user_can_post() && !is_archive() ) : ?>
             <div class="avatar" style="display:table; margin: 0 auto; padding-bottom:3.264em;">
		<?php echo get_avatar( get_current_user_id(), 240 ); ?>
		<?php global $current_user;
                    get_currentuserinfo();
                    echo '<div style="padding-left:3em; padding-top:1em; color:grey;"><h2>@' . $current_user->display_name . '</h2>';
                    echo '<p>' . $current_user->user_firstname . " ";
                    echo $current_user->user_lastname . "<br/>";
                    echo '<small>' . $current_user->user_email . '</small></p></div>';
     
                ?>
            </div>
        <?php endif; ?>        
        </div>
    <?php dynamic_sidebar( 'left-sidebar' ); ?>
    </div> <!-- // postbox -->
