<?php get_header(); ?>

      <div class="row">

        <div class="col-sm- blog-main">

          <?php
          if(have_posts()):
            while(have_posts()):
              the_post();
              get_template_part('content-single', get_post_format());
            endwhile;
          endif;
          ?>

        </div><!-- /.blog-main -->

      </div><!-- /.row -->

<?php get_footer(); ?>
