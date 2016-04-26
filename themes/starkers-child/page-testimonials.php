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
 Template Name: Home
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<div class="grid">
	<div class="col-3-4">
		<?php
		$products_args = array( 'post_type' => 'post', 'order' => 'ASC', 'category_name' => 'testimony' , 'orderby' => 'menu_order', 'post_parent' =>0 );
		$products_loop = new WP_Query( $products_args );?>
		<?php while ( $products_loop->have_posts() ) : $products_loop->the_post();?>
			<div class="module white-background">
				<h3><?php the_title(); ?></h3>
				<?php the_content(); ?>
			</div>
		<?php endwhile ?>
	</div>
	<div class="col-1-4">
		<div class="module">
			<?php include ('parts/sidebar_secondary.php'); ?>
		</div>
	</div>
</div>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>
