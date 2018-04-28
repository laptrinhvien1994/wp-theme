<?php
get_header(); ?>
<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
Noi dung trang test
<?php
                $taxonomiesList = array(
                    'category' => 'Danh mục',
                    'loai-san-pham' => 'Loại sản phẩm',
                    'loai-cham-soc' => 'Sản phẩm dành cho'
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
                            echo '<li><a href="'.$link.'">'.$term->name.' ( '.$posts_count.' bài viết )'.'</a></li>';
                        }
                        echo '</ul>'; 
                    }   
                }
            ?>


		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php get_footer();
