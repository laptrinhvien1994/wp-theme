	<?php
	print_r(get_queried_object());
	$term_obj = get_queried_object();

	//get taxonomy, term from url.
	$taxonomy = $term_obj->taxonomy;
	$term = $term_obj->slug;


	echo '<br/>';
	//get posts of this tax and term.
	$args = array(
		'posts_per_page' => 1,
		'post_type' => 'post',
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
	?>