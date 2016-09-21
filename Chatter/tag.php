<?php
/**
 * Tag Archive Template.
 *
 * @package Chatter
 */
?>
<?php get_header(); ?>
<?php $tag_obj = $wp_query->get_queried_object(); ?>
<div class="sleeve_main">
    <div id="main">
	<h2><?php printf( __( 'Tagged: %s', 'Chatter' ), single_tag_title( '', false) ); ?>
            <span class="controls">
                <a href="#" id="togglecomments"> <?php _e( 'Toggle Comment Threads', 'Chatter' ); ?></a> | <a href="#directions" id="directions-keyboard"><?php _e( 'Keyboard Shortcuts', 'Chatter' ); ?></a>
            </span>
        </h2>
        <?php if ( have_posts() ) : ?>
	<ul id="postlist">
            <?php while ( have_posts() ) : the_post(); ?>
            <?php Chatter_load_entry(); ?>
            <?php endwhile; ?>
        </ul>
        <?php else : ?>
        <div class="no-posts">
            <h3><?php _e( 'No posts found!', 'Chatter' ); ?></h3>
        </div>
        <?php endif ?>
	<div class="navigation">
            <p class="nav-older"><?php next_posts_link( __( '&larr; Older posts', 'Chatter' ) ); ?></p>
            <p class="nav-newer"><?php previous_posts_link( __( 'Newer posts &rarr;', 'Chatter' ) ); ?></p>
        </div>
    </div> <!-- main -->
</div> <!-- sleeve -->
<?php get_footer(); ?>
