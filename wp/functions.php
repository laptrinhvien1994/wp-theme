<?php
	function add_scripts_styles(){
		//bootstrap
		wp_enqueue_style('bootstrap', get_template_directory_uri().'/css/bootstrap.min.css', array(), '1', 'all');
		//stylesheet
		wp_enqueue_style('blog', get_template_directory_uri().'/css/blog.css', array(), '1', 'all');
	}

	add_action('wp_enqueue_scripts', 'add_scripts_styles');

	function add_Google_font(){
		//font Open Sans
		wp_register_style('OpenSans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800%27)');
		wp_enqueue_style('OpenSans');
	}

	add_action('wp_print_styles', 'add_Google_font');

	// add_theme_support('title-tag');


	// function custom_settings_add_menu(){
	// 	add_menu_page('Custom settings', 'new settings', 'manage_options', 'custom_settings_page','custom_settings_page', null, 99);
	// }

	add_action('admin_menu', function(){
		add_menu_page('Custom settings', 'new settings', 'manage_options', 'slug_name','custom_settings_page', null, 99);
	});

	function custom_settings_page(){ ?>
		<div class="wrap">
			<h1> Custom settings </h1>
			<form method="post" action="options.php">
				<?php
					settings_fields('section');
					do_settings_sections('slug_name');
					submit_button();
				 ?>
			</form>
		</div>
	<?php }

	function add_custom(){
		settings_fields('group-name')
		add_settings_section('id_section', 'Nhập vào title', null, 'general');
		add_settings_field('id_field', 'Nhập vào giá trị gì đó', null, 'general', 'section', null);
		register_setting('group-name', )
	}

	add_action('admin_init', 'add_custom');