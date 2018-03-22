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

	add_action('admin_menu', function(){
		add_menu_page('Tieu de cua trang', 'Tieu de menu', 'manage_options', 'option-slug', function(){
			?>
			<h1>Nhap tieu de</h1>
			<p>Nhap mot vai thong tin khac nua</p>
			<form method="post" action="options.php">
			<?php
           settings_fields( 'option-slug' );
           do_settings_sections( 'option-slug' );      
           submit_button(); 
           echo '</form>';
		}, '', 99);
	});


	add_action('admin_init', function(){
		//add section vào page
		add_settings_section('id_section', 'Tieu de cua section', function(){
			?>
			Noi dung cua section
			<?php
		}, 'option-slug');
		//add field vào section
		add_settings_field('id_field', 'Tieu de cua field', function(){
			?>
			<input name="id_field" id="id_field" value="<?php echo get_option('id_field'); ?>"></input>
			<?php
		}, 'option-slug', 'id_section', null);
		//đăng ký
		register_setting('option-slug', 'id_field');
	});

