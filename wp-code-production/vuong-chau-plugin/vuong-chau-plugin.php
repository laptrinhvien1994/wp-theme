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

		$posts = new WP_Query($args);

		//loop through
		$content = '';
		while($posts->have_posts()){
			$posts->the_post();
			$id = get_the_ID();
			$the_post_name = VuongChauHelper::substring(get_the_title(), 60);
			$the_permalink = get_the_permalink();
			$thumbnail_size = array('width' => 100, 'height' => 100);
			$the_thumbnail = get_the_post_thumbnail($id,array($thumbnail_size['width'], $thumbnail_size['height']),array('alt' => 'mỹ phẩm nước hoa chính hãng'));
			$default_thumbnail = 'http://javascript.com:7878/wp-content/uploads/2018/02/marguerite-729510_960_720-768x499.jpg';
			$the_thumbnail = $the_thumbnail != '' ? $the_thumbnail : "<img alt='mỹ phẩm nước hoa chính hãng' width='${thumbnail_size["width"]}' height='${thumbnail_size["height"]}' src='${default_thumbnail}'>";

			$content = $content."
			<article class='vuongchau-each-post-sidebar'>
				<span class='vuongchau-thumbnail-sidebar'>${the_thumbnail}</span>
				<span class='vuongchau-thumbnail-post-title'><a href='${the_permalink}'>${the_post_name}</a></span>
			</article>";
		}
		wp_reset_postdata();
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
		$title = '<span style="border-bottom: 5px double #0b9416; font-family: \'Seoge UI\'; text-transform: uppercase;
		color: #0b9416">'.$title.'</span>';
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
		
		$content = '<div class="vuongchau-title"><h2>Các bài viết mới nhất</h2></div>';
		$posts = new WP_Query($args);
		while($posts->have_posts()){
			$posts->the_post();
			//$content = $content.'<br/>';
			//$content = $content.'<h2>'.get_the_title().'</h2>';
			$id = get_the_ID();
			$the_post_name = get_the_title();
			$the_permalink = get_the_permalink();
			$thumbnail_size = array('width' => 800, 'height' => 400);
			$the_thumbnail = get_the_post_thumbnail($id,array($thumbnail_size['width'], $thumbnail_size['height']),array('alt' => 'mỹ phẩm nước hoa chính hãng'));
			$default_thumbnail = 'http://wp.wp/wp-content/uploads/2012/06/dsc20050604_133440_34211.jpg';
			$the_thumbnail = $the_thumbnail != '' ? $the_thumbnail : "<img alt='mỹ phẩm nước hoa chính hãng' width='${thumbnail_size["width"]}' height='${thumbnail_size["height"]}' src='${default_thumbnail}'>";
			$the_date = get_the_time('d/m/Y');
			$the_author_link = get_the_author();
			$the_author = get_the_author();
			$the_excerpt = get_the_excerpt();
			$taxs = get_post_taxonomies($id);
			$tax_will_display = array('loai-san-pham', 'loai-cham-soc');
			$html_terms = '';
			$list_term_name = array();
			foreach ($taxs as $tax) {
				if(in_array($tax, $tax_will_display)){
					$terms = get_the_terms($id, $tax);
					if($terms != false && count($terms) > 0){
						foreach ($terms as $term) {
							$link = get_site_url()."/".$tax."/".$term->slug."/";
							$text = $term->name;
							$html_link_term = "<a href='${link}'>${text}</a>";
							array_push($list_term_name, $html_link_term);
						}
					}
				}
			}
			$html_terms = $html_terms.join($list_term_name, "</span><span class='each-category' title='Nhấp chọn để vào danh mục'>");
			$html_terms = $html_terms != '' ? $html_terms : '<a href="javascript:void(0);" style="cursor:default;">Chưa phân nhóm</a>';

			$content = $content."
			<article class='each-post'>
			<div class='thumbnail'>${the_thumbnail}</div>

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
			    <span class='posted-date'><i class='fl-button-icon fl-button-icon-before fa fa fa-chevron-right'></i>${the_date}</span>
			    <span class='posted-author'>${the_author}</span>
			    <span class='each-category' title='Nhấp chọn để vào danh mục'>${html_terms}</span>
			</div>
			<!-- .entry-meta -->
			<div class='entry-summary'>
			    <p class='post-excerpt'>${the_excerpt}.</p>
			    <p><a class='button' href='${the_permalink}' aria-label='Continue reading Scheduled'>Đọc bài viết này</a></p>
			</div>
			<!-- .entry-summary -->
			</article>";
		}
		wp_reset_postdata();
		return $content;
	}
	add_shortcode( 'newest_posts', 'create_newest_posts_shortcode' );

	/*
	* Thêm CSS và JS cho site.
	*/
	add_action('wp_enqueue_scripts', 'vuongchau_enqueue_scripts');

	function vuongchau_enqueue_scripts(){
		wp_enqueue_style('vuongchau-style', get_stylesheet_directory_uri().'/assets/css/vuongchau-style.css');
		wp_enqueue_script('vuongchau-script', get_stylesheet_directory_uri().'/assets/javascript/vuongchau-main.js');
	}

	/*
	* Chỉnh excerpt thêm [...] ở cuối.
	*/
	add_filter('get_the_excerpt', 'filter_excerpt');

	function filter_excerpt($excerpt){
		return $excerpt."  [...]";
	}

	?>