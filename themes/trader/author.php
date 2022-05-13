<?php get_header(); ?>

<?php
if( have_posts() ) :
    // THE WP LOOP
    while( have_posts() ) :
    the_post(); ?>
<h2><?php the_title(); ?></h2>
<?php the_content(); ?>

    <!-- end of wp loop -->
    <?php endwhile; ?>

<?php the_posts_navigation(); ?>

<?php else : ?>
        <p>No posts found</p>
<?php endif; ?>

<?php get_footer(); ?>