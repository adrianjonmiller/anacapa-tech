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
 * Template Name: Home
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<div class="grid flexslider" data-behavior="flexslider">
	<ul class="slides" id="banner">
		<?php
		$args = array( 'post_type' => 'banner', 'order' => 'ASC', 'orderby' => 'menu_order' );
		$loop = new WP_Query( $args );?>
		<?php while ( $loop->have_posts() ) : $loop->the_post();?>
		<li>
			<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail('full');
				} 
			?>
			<div class="banner-text">
				<h2><p  class="flex-caption"><?php the_title(); ?></p></h2>
			</div>
		</li>
		<?php endwhile; ?>
	</ul>
</div>

<div class="grid">
	<div class="col-3-4">
		<div class="grid products obj_sameHeights grid-inset" data-behavior="equalHeights">
		<?php
		$products_args = array( 'post_type' => 'post', 'order' => 'ASC', 'category_name' => 'home-page-column' , 'orderby' => 'menu_order', 'post_parent' =>0 );
		$products_loop = new WP_Query( $products_args );?>
		<?php while ( $products_loop->have_posts() ) : $products_loop->the_post();?>
			<div class="col-1-3 obj_height">
				<div class="module product">
					<div class="mobile-half">
						<h4><a href="<?php echo(types_render_field("image-url", array('raw'=> 'true'))); ?>" target="<?php echo(types_render_field("new-window")); ?>"><?php the_title(); ?></a></h4>
						<?php the_content(); ?>
					</div>
					<div class="mobile-half product-image">
					
					<a href="<?php echo(types_render_field("image-url", array('raw'=> 'true'))); ?>" target="<?php echo(types_render_field("new-window")); ?>">
					<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail('full');
						} 
					?>
					</a>
					</div>
				</div>
			</div>
		<?php endwhile ?>
		</div>
	</div>
	<div class="col-1-4">
		<div class="module">
			<?php include ('parts/sidebar_primary.php'); ?>
		</div>
	</div>
</div>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
<?php the_content(); ?>
<?php endwhile; ?>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>
