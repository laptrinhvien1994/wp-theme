<?php

get_header(); ?>

<div id="primary" class="content-area">

	<main id="main" class="site-main" role="main">

		<?php
	$term_obj = get_queried_object();

	//get taxonomy, term from url.
	$taxonomy = $term_obj->taxonomy;
	$term = $term_obj->slug;

	//config variables.
	$posts_per_page = get_option('posts_per_page');
	$total_posts = $term_obj->count;
	$total_pages = ceil($total_posts/$posts_per_page);
	$page_index = get_query_var('paged') ? get_query_var('paged') : 1;

	//get posts of this tax and term.
	$args = array(
		'posts_per_page' => $posts_per_page,
		'post_type' => 'post',
		'paged' => $page_index,
		'tax_query' => array(
			array( 
				'taxonomy' => $taxonomy,
				'terms' => $term,
				'field' => 'slug',
				'operator' => 'AND'
				)
			)
		);
	$posts = get_posts($args);


	//loop through each post
	if(count($posts) > 0){
		echo '<ul>';
		foreach($posts as $post){
			//print_r($post);
			$url = get_site_url()."/".$post->post_name;
			$title = $post->post_title;
			echo '<li><a href="'.$url.'">'.$title.'</a></li>';
			$thumbnail_url = get_the_post_thumbnail($post, array(200, 400));
			echo $thumbnail_url;
		}
		echo '</ul>';
	}
 

	//display pagination
	  $big = 999999999; // need an unlikely integer
	  echo '<div class="paginate-links">';
	    echo paginate_links( array(
	    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	    'format' => '?paged=%#%',
	    'prev_text' => __('<<'),
	    'next_text' => __('>>'),
	    'current' => max( 1, get_query_var('paged') ),
	    'total' => $total_pages
	    ) );
	  echo '</div>';
	?>
		
	</main><!-- #main -->

</div><!-- #primary -->

<?php get_sidebar(); ?>

<?php get_sidebar( 'tertiary' ); ?>

<?php get_footer(); ?>
