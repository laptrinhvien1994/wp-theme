
<?php
if(have_posts()):
	while(have_posts()):
		the_post();
	?>
	<div class="blog-post">
		<h2 class="blog-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="blog-post-meta"><?php the_date(); ?> by <a href="#"><?php the_author(); ?></a></p>

		<p><?php the_content(); ?>.</p>
		<hr>

		<!-- the rest of the content -->
	</div><!-- /.blog-post -->
	<?php
	endwhile;
	get_template_part('pagination');
	endif;
	?>

if(have_posts()):
	while(have_post()):
		the_post();
	endwhile
else
esc_html_e	
endif