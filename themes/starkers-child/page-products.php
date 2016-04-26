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
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<div class="grid">
	<div class="col-3-4">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<h2>
				<?php the_title(); ?>
			</h2>
		<?php endwhile; ?>
		<ul>
			<?php
			$args = array( 'post_type' => 'product', 'order' => 'ASC', 'orderby' => 'menu_order', 'post_parent' => 0 );
			$loop = new WP_Query( $args );
			while ( $loop->have_posts() ) : $loop->the_post();?>
				
				<li class="grid white-background">
					<div class="col-1-4">
						<div class="module" data-behavior="maxImageWidth">
							<?php  if ( has_post_thumbnail() ) {
								the_post_thumbnail('thumbnail');
							} 
							?>
						</div>
					</div>
					<div class="col-3-4">
						<article class="module">
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<?php the_content(); ?>
						</article>
					</div>
				</li>
			<?php endwhile; ?>
		</ul>
	</div>
	<div class="col-1-4">
		<?php include ('parts/sidebar_primary.php'); ?>
		<?php include ('parts/sidebar_ads.php'); ?>
	</div>
</div>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>
