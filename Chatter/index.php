<?php
/**
 * Main template file.
 *
 * @package Chatter
 */
?>
<?php get_header(); ?>
<div class="sleeve_main">
	<?php locate_template( array( 'left-bar.php' ), true ); ?>
	<div id="main">
		<?php if ( Chatter_user_can_post() && !is_archive() ) : ?>
                <div id="submission">
                    <?php locate_template( array( 'comment-post.php' ), true ); ?>
                </div><?php endif; ?>
	        <?php if (function_exists('user_submitted_posts')) user_submitted_posts(); ?>
                <ul id="postlist">
                    <div id="submission">
                    </div>
                    <?php if ( is_home() or is_front_page() ) : ?>
                    <?php if ( Chatter_get_page_number() > 1 ) printf( __( 'Page %s', 'Chatter' ), Chatter_get_page_number() ); ?>
                    <?php else : ?>
                    <?php printf( _x( 'Updates from %s', 'Month name', 'Chatter' ), get_the_time( 'F, Y' ) ); ?>
                    <?php endif; ?>
                    <?php if ( have_posts() ) : ?>
                    <?php while ( have_posts() ) : the_post(); ?>
                    <?php Chatter_load_entry(); ?>
                    <?php if (get_post_status() == 'pending') : ?>
                    <p><small>The post above is awaiting moderation.</small></p>
                    <?php endif; ?>
                    <?php endwhile; ?>
                    <?php else : ?>
			<li class="no-posts">
		    	<h3><?php _e( 'No posts yet!', 'Chatter' ); ?></h3>
			</li>
		<?php endif; ?>
		</ul>
		<div class="navigation">
			<p class="nav-older"><?php next_posts_link( __( '&larr; Older posts', 'Chatter' ) ); ?></p>
			<p class="nav-newer"><?php previous_posts_link( __( 'Newer posts &rarr;', 'Chatter' ) ); ?></p>
		</div>
	</div> <!-- main -->
</div> <!-- sleeve --><div id='sidebar'>
    <?php dynamic_sidebar('right-sidebar'); ?>
</div>
<?php get_footer(); ?>
