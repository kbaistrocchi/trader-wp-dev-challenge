<?php get_header(); ?>

<main>
<?php
if(have_posts() ) :
    // THE WP LOOP
    while( have_posts() ) :
        the_post(); ?>
        <h2><?php the_title(); ?></h2>
        <p><em>written by: <?php echo get_the_author(); ?></em></p>
        <section>
            <?php the_content(); ?>
        </section>

    <!-- end of wp loop -->
    <?php endwhile; ?>

    <?php the_posts_navigation(); ?>

<?php else : ?>
        <p>No posts found</p>
<?php endif; ?>
</main>

<?php get_footer(); ?>