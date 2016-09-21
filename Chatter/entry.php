<?php
/**
 * Displays the content and meta information for a post object.
 *
 * @package Chatter
 */
?>
<li id="prologue-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	/*
	 * Post meta
	 */
	if ( ! is_page() ):
		$author_posts_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
		$posts_by_title   = sprintf(
			__( 'Posts by %1$s ( @%2$s )', 'Chatter' ),
			get_the_author_meta( 'display_name' ),
			get_the_author_meta( 'user_nicename' )
		); ?>
		<a href="<?php echo esc_attr( $author_posts_url ); ?>" title="<?php echo esc_attr( $posts_by_title ); ?>" class="post-avatar">
		<?php echo get_avatar( get_the_author_meta('user_email'), 24 ); ?>
		</a>
                <?php endif; ?>
	<h4>
		<?php if ( ! is_page() ): ?>
			<a href="<?php echo esc_attr( $author_posts_url ); ?>" title="<?php echo esc_attr( $posts_by_title ); ?>"><?php the_author(); ?></a>
		<?php endif; ?>
		<?php if (get_post_status() == 'publish') : ?>
                <?php if ( ! is_page() ) {
                        echo '<span class="light"><small><a href="'; echo the_permalink() .'" class="thepermalink ">'. Chatter_chatter_time_ago() .'</a></small></span>';
			} ?>
                <?php endif; ?><span class="meta">
			<span class="actions">
				<a href="<?php the_permalink(); ?>" class="thepermalink<?php if ( is_singular() ) { ?> printer-only<?php } ?>" title="<?php esc_attr_e( 'Permalink', 'Chatter' ); ?>"><?php _e( 'Permalink', 'Chatter' ); ?></a>
				<?php
				if ( ! is_singular() )
					$before_reply_link = ' | ';
				if ( comments_open() && ! post_password_required() ) {
						echo post_reply_link( array(
							'before'        => isset( $before_reply_link ) ? $before_reply_link : '',
							'after'         => '',
							'reply_text'    => __( 'Reply', 'Chatter' ),
							'add_below'     => 'comments'
						), get_the_ID() );
				}
				if ( current_user_can( 'edit_post', get_the_ID() ) ) : ?> | <a href="<?php echo ( get_edit_post_link( get_the_ID() ) ); ?>" class="edit-post-link" rel="<?php the_ID(); ?>" title="<?php esc_attr_e( 'Edit', 'Chatter' ); ?>"><?php _e( 'Edit', 'Chatter' ); ?></a>
				<?php endif; ?>
				<?php do_action( 'Chatter_action_links' ); ?>
			</span>
			<?php if ( is_object_in_taxonomy( get_post_type(), 'post_tag' ) ) : ?>
                        <?php endif; ?>
		</span>
	</h4>
	<div id="content-<?php the_ID(); ?>" class="postcontent">
	<?php
	/*
	 * Check the post format and display content accordingly.
	 * The value should be a valid post format or one of the back compat categories.
	 */
	switch ( Chatter_get_post_format( $post->ID ) ) {
		case 'status':
		case 'link':
			the_content( __( '(More ...)' , 'Chatter' ) );
			break;
		case 'quote':
			Chatter_quote_content();
			break;
		case 'post':
		case 'standard':
		default:
			Chatter_title();
			the_content( __( '(More ...)' , 'Chatter' ) );
			break;
	} ?>
	</div>
	<?php
	$comment_field = '<div class="form"><textarea id="comment" class="expand50-100" name="comment" cols="45" rows="3"></textarea></div> <label class="post-error" for="comment" id="commenttext_error"></label>';
	$comment_notes_before = '<p class="comment-notes">' . ( get_option( 'require_name_email' ) ? sprintf( ' ' . __( 'Required fields are marked %s', 'Chatter' ), '<span class="required">*</span>' ) : '' ) . '</p>';
	$Chatter_comment_args = array(
		'title_reply'           => __( 'Reply', 'Chatter' ),
		'comment_field'         => $comment_field,
		'comment_notes_before'  => $comment_notes_before,
		'comment_notes_after'   => '<span class="progress spinner-comment-new"></span>',
		'label_submit'          => __( 'Reply', 'Chatter' ),
		'id_submit'             => 'comment-submit',
	);
	?>
	<?php if ( get_comments_number() > 0 && ! post_password_required() ) : ?>
		<div class="discussion" style="display: none">
			<p>
				<?php Chatter_discussion_links(); ?>
				<a href="#" class="show-comments"><?php _e( 'Toggle Comments', 'Chatter' ); ?></a>
			</p>
		</div>
	<?php endif;
	wp_link_pages( array( 'before' => '<p class="page-nav">' . __( 'Pages:', 'Chatter' ) ) ); ?>
	<div class="bottom-of-entry">&nbsp;</div>
	<?php if ( Chatter_is_ajax_request() ) : ?>
		<ul id="comments-<?php the_ID(); ?>" class="commentlist inlinecomments"></ul>
	<?php else :
		comments_template();
		$pc = 0;
		if ( Chatter_show_comment_form() && $pc == 0 && ! post_password_required() ) :
			$pc++; ?>
			<div class="respond-wrap" <?php echo ( ! is_singular() ) ? 'style="display: none; "' : ''; ?>>
				<?php comment_form( $Chatter_comment_args ); ?>
			</div><?php
		endif;
	endif; ?>
</li>
