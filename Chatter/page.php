<?php
/**
 * Static page template.
 *
 * @package Chatter
 */
?>
<?php get_header(); ?>
<div class="sleeve_main">
	<div id="page-main">
		<h2><?php the_title(); ?></h2>
		<ul id="postlist">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php Chatter_load_entry(); ?>
			<?php endwhile; ?>
		<?php endif; ?>
		</ul>
	</div> <!-- main -->
</div> <!-- sleeve -->
<?php get_footer(); ?>
