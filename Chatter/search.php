<?php
/**
 * Search result template.
 *
 * @package Chatter
 */
?>
<?php get_header(); ?>
<div class="sleeve_main">
	<div id="page-main">
            <h2>
                <?php printf( __( 'Search Results for: %s', 'Chatter' ), get_search_query() ); ?>
            </h2>
            <ul id="postlist">
                <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); ?>
                <?php Chatter_load_entry(); ?>
                <?php endwhile; ?>
                <li class="post"><h2>Search again?</h2><?php get_search_form(); ?>
                    <div id="clearspace"></div>
                </li>
                <?php else : ?>
                <li class="post">
                    <div class="no-posts">
                        <h3><?php _e( 'No posts found!', 'Chatter' ); ?></h3>
                        <p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'Chatter' ); ?></p>
                        <?php get_search_form(); ?>
                        <div id="clearspace"></div>
                    </div>
                </li>
                <?php endif ?>
                <div class="navigation">
                    <p class="nav-older"><?php next_posts_link( __( '&larr; Older posts', 'Chatter' ) ); ?></p>
                    <p class="nav-newer"><?php previous_posts_link( __( 'Newer posts &rarr;', 'Chatter' ) ); ?></p>
                </div>
            </ul>
        </div> <!-- main -->
</div> <!-- sleeve -->
<?php get_footer(); ?>
