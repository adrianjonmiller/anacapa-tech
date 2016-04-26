<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 * Template Name: Secondary Sidebar
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<div class="grid">
	<div class="col-3-4">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<article class="module white-background">
		<?php the_content(); ?>
	</article>
<?php endwhile; ?>
	</div>
	<div class="col-1-4">
		<?php include ('parts/sidebar_secondary.php'); ?>
	</div>
</div>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>
