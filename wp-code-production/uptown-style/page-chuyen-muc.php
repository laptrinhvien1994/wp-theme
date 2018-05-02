<?php

get_header(); ?>

<div id="primary" class="content-area">

	<main id="main" class="site-main" role="main">

			<?php
				$taxonomiesList = array(
				    //'category' => 'Danh mục',
				    'loai-san-pham' => 'Các chuyên mục về sản phẩm',
				    'loai-cham-soc' => 'Các chuyên mục về chăm sóc'
				    );
				$taxonomies = get_taxonomies();
				foreach ( $taxonomies as $taxonomy ) {
				    if(array_key_exists($taxonomy, $taxonomiesList)){
				        echo '<h3>' . $taxonomiesList[$taxonomy] . '</h3>';
				        echo '<ul>';
				        $terms = get_terms(array(
				            'taxonomy' => $taxonomy,
				            'hide_empty' => false,
				            ));

				        foreach ($terms as $term) { #print_r($term);
				        	$link = get_site_url().'/'.$term->taxonomy.'/'.$term->slug;
				        	$posts_count = (string)$term->count;
							$text = 'Chuyên mục về '.$term->name;
				            echo '<li><a href="'.$link.'">'.$text.' ( '.$posts_count.' bài viết )'.'</a></li>';
				        }
				        echo '</ul>'; 
				    }   
				}
			?>

	</main><!-- #main -->

</div><!-- #primary -->

<?php get_sidebar(); ?>

<?php get_sidebar( 'tertiary' ); ?>

<?php get_footer(); ?>
