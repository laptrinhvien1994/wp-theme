	<?php
	/*
	Plugin Name:  Vuong Chau Shop Plugin
	Plugin URI:   myphamvuongchau.com
	Description:  This plugin was created by Quang, to make Vuong Chau Shop site more flexible and customizable.
	Version:      1.0.0
	Author:       Minh Quang
	Author URI:   myphamvuongchau.com
	License:      
	License URI:  
	Text Domain:  
	Domain Path: 
	*/




	/*
	* Tạo taxonomy
	*/
	function create_SanPham_taxonomy(){
		$labels = array(
			'name' => 'Các loại sản phẩm',
			'singular' => 'Loại sản phẩm đây là ít nè',
			'search_items'      => 'Tìm kiếm loại sản phẩm',
			'all_items'         => 'Tất cả các loại sản phẩm',
			'parent_item'       => 'Loại sản phẩm cha',
			'parent_item_colon' => 'Loại sản phẩm cha:',
			'edit_item'         => 'Chỉnh sửa loại sản phẩm',
			'update_item'       => 'Cập nhật loại sản phẩm',
			'add_new_item'      => 'Thêm mới một loại sản phẩm',
			'new_item_name'     => 'Loại sản phẩm mới',
			'menu_name'         => 'Loại sản phẩm'
			);
		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'public' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud' => true
			);
		register_taxonomy('loai-san-pham', 'post', $args);
	}

	function create_ChamSoc_taxonomy(){
		$labels = array(
			'name' => 'Chăm sóc',
			'singular' => 'Chăm sóc',
			'menu_name' => 'Chăm sóc'
			);
		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'public' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud' => true
			);
		register_taxonomy('loai-cham-soc', 'post', $args);
	}

	function create_custom_taxonomies(){
		create_SanPham_taxonomy();
		create_ChamSoc_taxonomy();
	}

	add_action('init', 'create_custom_taxonomies', 0);


	/*
	* Hiển thị template đang được dùng cho trang
	*/
	add_action('wp_head', 'show_template');
	function show_template() {
		if( current_user_can( 'manage_options' ) ){
			global $template;
			echo basename($template);
		}
	}



		/*
		* Thêm shortcode cho widget
		*/
	function create_show_related_posts_shortcode( $atts ) {

		// Attributes 
		$atts = shortcode_atts(
			array(
				'post_number' => '5',
				),
			$atts
			);

		extract($atts);

		$queried_obj = get_queried_object();
		$args = null;
		//if page template is post then query related post 
		if($queried_obj instanceof WP_Post){
			if($queried_obj->post_type == 'post'){
				$categories = get_the_category($queried_obj->ID);
				$category_ids = array();
				foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
				$args = array(
					'posts_per_page' => $post_number,
					'post_type' => 'post',
					'category__in' => $category_ids,
					'post__not_in' => array($queried_obj)
					);
			}
			else{
				$args = array(
					'posts_per_page' => $post_number,
					'post_type' => 'post',
					'meta_key' => 'post_views_count',
					'order' => 'DESC',
					'orderby' => 'meta_value_num'
					);
			}
		}
		//if page template is term (category, home) then query most viewed post 
		else{ //if($term_obj instanceof WP_Term){
			$args = array(
				'posts_per_page' => $post_number,
				'post_type' => 'post',
				'meta_key' => 'post_views_count',
				'order' => 'DESC',
				'orderby' => 'meta_value_num'
				);
		}

		$posts = get_posts($args);

		//loop through
		$content = '';
		$count = count($posts);
		if($count > 0){
			foreach($posts as $post){
				$content = $content.'<div>';

		        //get thumbnail
				$thumbnail = get_the_post_thumbnail($post, array('100, 100'));
				$content = $content.$thumbnail;

		        //get link
				$url = get_site_url()."/".$post->post_name;
				$title =  VuongChauHelper::substring($post->post_title,30);
				$content = $content.'<span><a href="'.$url.'">'.$title.'</a></span>';
				$content = $content.'</div>';
			}

		//       //show "Xem thêm" button.
			// if($count == $post_number){
			// 	$url = get_site_url();
			// 	$content = $content.'<div><a href="'.$url.'">Xem thêm<a/></div>';
			// }
		}
		return $content;
	}
	add_shortcode( 'show_related_posts', 'create_show_related_posts_shortcode' );


	/*
	* Đếm số lượt lượng truy cập vào post
	*/
	function set_post_views($postID) {
		$count_key = 'post_views_count';
		$count = get_post_meta($postID, $count_key, true);
		if($count==''){
			$count = 0;
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '0');
		}else{
			$count++;
			update_post_meta($postID, $count_key, $count);
		}
	}
		//To keep the count accurate, lets get rid of prefetching
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);


	function track_post_views ($post_id) {
		if ( !is_single() ) return;
		if ( empty ( $post_id) ) {
			global $post;
			$post_id = $post->ID;    
		}
		set_post_views($post_id);
	}
	add_action( 'wp_head', 'track_post_views');


	/*
	* Chỉnh sửa title cho widget
	*/
	add_filter ( 'widget_title', 'edit_widget_title' );
	function edit_widget_title( $title ) {
		if($title == 'Bài viết liên quan'){
			$queried_obj = get_queried_object();
			if($queried_obj instanceof WP_Post){
				if($queried_obj->post_type == 'post'){
					$title = 'Có thể bạn quan tâm';
				}
				else{
					$title = 'Bài viết hay nhất';
				}
			}
			else{ //$queried_obj instanceof WP_Term
				$title = 'Bài viết hay nhất';	
			}
		}
		$title = '<span style="color: green;">'.$title.'</span>';
		return $title;
	}

	/*
	* Class chứa các phương thức bổ trợ trong quá trình custom.
	*/
	class VuongChauHelper{
		static function substring($str, $char_length){
			if(strlen($str) > $char_length){
				$str = substr($str, 0, $char_length - 3)."...";
			}
			return $str;
		}
	}

	/*
	* Shortcode lấy các bài viết mới nhất.
	*/
	function create_newest_posts_shortcode( $atts ) {

		// Attributes
		$atts = shortcode_atts(
			array(
				'post_number' => '10',
			),
			$atts
		);

		extract($atts);

		$args = array(
			'posts_per_page' => $post_number,
			'post-type' => 'post',
			'orderby' => 'date'
			);
		
		$content = '<h1>Các bài đăng mới nhất</h1><br/>';
		$posts = new WP_Query($args);
		while($posts->have_posts()){
			$posts->the_post();
			//$content = $content.'<br/>';
			//$content = $content.'<h2>'.get_the_title().'</h2>';
			$the_post_name = get_the_title();
			$the_permalink = get_the_permalink();
			$the_date = get_the_time();
			$the_author_link = get_the_author();
			$the_author = get_the_author();
			$the_excerpt = get_the_excerpt();
			$id = get_the_ID();
			$taxs = get_post_taxonomies($id);
			$tax_will_display = array('loai-san-pham', 'loai-cham-soc');
			$categories = '';
			foreach ($taxs as $tax) {
				if(in_array($tax, $tax_will_display)){
					$terms = get_the_terms($id, $tax);
					$category_text = array();
					if($terms != false && count($terms) > 0){
						foreach ($terms as $term) {
							array_push($category_text, $term->name);
						}
					}
					$categories = $categories.join($category_text, "</span><span class='each-category'>");
				}
			}
			echo $categories;
			echo '<br/>';
			echo '<br/>';

			$content = $content."<article>
	<header class='entry-header'>
	    <div class='entry-header-row'>
	        <div class='entry-header-column'>
	            <h2 class='entry-title'><a href='${the_permalink}'>${the_post_name}</a></h2>
	        </div>
	        <!-- .entry-header-column -->
	    </div>
	    <!-- .entry-header-row -->
	</header>
	<!-- .entry-header -->
	<div class='entry-meta'>
	    <span class='posted-date'>Đăng vào lúc ${the_date}</span>
	    <span class='posted-author'><span>Được đăng bởi: ${the_author}</span></span>
	</div>
	<!-- .entry-meta -->
	<div class='entry-summary'>
	    <p>${the_excerpt}.</p>
	    <p><a class='button' href='${the_permalink}' aria-label='Continue reading Scheduled'>Xem thêm →</a></p>
	</div>
	<!-- .entry-summary -->
	</article>";





		}
		wp_reset_postdata();
		return $content;
	}
	add_shortcode( 'newest_posts', 'create_newest_posts_shortcode' );
	?>




	<?php
	$str = '<article>
	<header class="entry-header">
	    <div class="entry-header-row">
	        <div class="entry-header-column">
	            <h2 class="entry-title"><a href="${the_permalink}">${the_post_name}</a></h2>
	        </div>
	        <!-- .entry-header-column -->
	    </div>
	    <!-- .entry-header-row -->
	</header>
	<!-- .entry-header -->
	<div class="entry-meta">
	    <span class="posted-date">${the_date}</span>
	    <span class="posted-author"><a href="${the_author_link}" title="Được đăng bởi ${the_author}" rel="author">${the_author}</a></span>
	    <span class="comments-number">
			<a href="${the_permalink}#respond" class="comments-link">Bình luận bài viết này</a>
		</span>
	</div>
	<!-- .entry-meta -->
	<div class="entry-summary">
	    <p>${the_excerpt}.</p>
	    <p><a class="button" href="${the_permalink}" aria-label="Continue reading Scheduled">Xem thêm →</a></p>
	</div>
	<!-- .entry-summary -->
	</article>';
	?>
