<?php
/**
 * @package WordPress
 * @subpackage Athene
 */

get_header(); ?>

		<div id="primary" class="container_16">
			<div id="content" class="grid_10 alpha" role="main">
				<?php the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
			</div><!-- #content -->
			
            <div id="comments" class="grid_6 omega">
                <?php comments_template( '', true ); ?>
            </div><!-- #comments -->
            
			<?php get_sidebar(); ?>

		</div><!-- #primary -->

<?php get_footer(); ?>