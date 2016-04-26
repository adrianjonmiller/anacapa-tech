<div id="sidebar-ads">
	<?php
	$terms = get_the_terms( $post->ID, 'ad-set' );
							
	if ( $terms && ! is_wp_error( $terms ) ) : 
	
		$ad_sets = array();
	
		foreach ( $terms as $term ) {
			$ad_sets[] = $term->name;
		}
							
		$ad_set = join( ", ", $ad_sets );
		
		echo $ad_set."success";
		
	endif; ?>

</div>
