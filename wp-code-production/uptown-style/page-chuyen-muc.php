<?php

get_header(); ?>

<div id="primary" class="content-area">

	<main id="main" class="site-main" role="main">
		<div id="vuongchau-chuyenmuc-content">
			<?php
				$taxonomiesList = array(
				    //'category' => 'Danh mục',
				    'loai-san-pham' => 'Các chuyên mục về sản phẩm',
				    'loai-cham-soc' => 'Các chuyên mục về chăm sóc'
				    );
				$taxonomies = get_taxonomies();
				foreach ( $taxonomies as $taxonomy ) {
				    if(array_key_exists($taxonomy, $taxonomiesList)){
				        echo '<h3 class="vuongchau-chuyenmuc-title">' . $taxonomiesList[$taxonomy] . '</h3>';
				        echo '<ul class="vuongchau-chuyenmuc-ul">';
				        $terms = get_terms(array(
				            'taxonomy' => $taxonomy,
				            'hide_empty' => false,
				            ));

				        foreach ($terms as $term) { #print_r($term);
				        	$link = get_site_url().'/'.$term->taxonomy.'/'.$term->slug;
				        	$posts_count = (string)$term->count;
							$text = wp_is_mobile() ? 'Về '.$term->name : 'Chuyên mục về '.$term->name;
							$post_count_text = wp_is_mobile() ? ' bài' : ' bài viết';
				            echo '<li class="vuongchau-chuyenmuc-item"><a href="'.$link.'">'.$text.' <span class="vuongchau-chuyenmuc-item-postcount"> ( '.$posts_count.$post_count_text.' ) </span> '.'</a></li>';
				        }
				        echo '</ul>'; 
				    }   
				}
			?>
		</div> <!-- #vuongchau-chuyenmuc-content-->
	</main><!-- #main -->

</div><!-- #primary -->

<?php get_sidebar(); ?>

<?php get_sidebar( 'tertiary' ); ?>

<?php get_footer(); ?>
