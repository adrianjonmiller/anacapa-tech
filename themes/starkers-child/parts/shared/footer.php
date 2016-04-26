</div>
</div>
<footer>
	<div class="container">
	<?php wp_nav_menu(array(
	    'container'=> 'nav',
	    'container_class' => '',
	    'container_id' => 'footer-menu',
	    'menu_id' =>'footermenu',
	    'menu_class' =>'',
	    'theme_location' => 'footer'
	)); ?>
	<div id="copywrite">&copy; <?php echo date("Y"); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</div>

	<!---<div class="social-media-links">
		 <a href="#" id="facebook" class="social-media-link obj_badge">
		facebook
		</a>
		<a href="#" id="twitter" class="social-media-link obj_badge">
		Twitter
		</a>
		<a href="#"id="google-plus" class="social-media-link obj_badge">
		Google Plus
		</a> 
	</div>  -->
    <?php dynamic_sidebar( 'footer' ); ?>
	</div>
</footer>
