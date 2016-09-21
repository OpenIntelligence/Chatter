<?php
/**
* 404 Post not found template.
*
* @package Chatter
*/
?>
<?php get_header(); ?>
<div class="sleeve_main">
    <div id="page-main">
        <h2><?php _e( 'Not Found', 'Chatter' ); ?></h2>
        <ul id="postlist">
            <li class="post">
                <p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'Chatter' ); ?></p>
                <?php get_search_form(); ?>
                <div id="clearspace"></div>
            </li>
        </ul>
    </div> <!-- page-main -->
</div> <!-- sleeve-main -->
<?php get_footer(); ?>
