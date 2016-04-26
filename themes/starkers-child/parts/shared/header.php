<header>
	<div class="container">
		<h1><a href="<?php echo home_url(); ?>"><img src="<?php echo get_stylesheet_directory_uri().'/img/logo.png'; ?>" alt="<?php bloginfo( 'name' ); ?>" /></a></h1>
		<h2 id="description"><?php bloginfo( 'description' ); ?></h2>
		<div id="search"><?php get_search_form(); ?></div>
		<?php wp_nav_menu(array(
		    'container'=> 'nav',
		    'container_class' => '',
		    'container_id' => 'primary',
		    'menu_id' =>'dropmenu',
		    'menu_class' =>'',
		    'theme_location' => 'primary'
		)); ?>
		<?php 
			wp_nav_menu_select(
			    array(
			        'theme_location' => 'primary',
			        'menu_class' => 'select-menu'
			    )
			);
		?>
		
	</div>
</header>
<div id="main" role="main">
	<div class="container">
