<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>

<div id="main">
	<div class="wrapper">
		<h1><a href="http://www.tomhoppe.com"><img src="http://www.tomhoppe.com/images/site_logo.png" border="0" width="247" height="45" alt="tomhoppe.com" /></a></h1>
		<div class="panel">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
		<h2><?php the_title(); ?></h2>
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

			</div>
		</div>
		<?php endwhile; endif; ?>
		<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>


		</div>
	</div>
</div>

<?php get_sidebar(); ?>

<div class="clearing"></div>

<?php get_footer(); ?>