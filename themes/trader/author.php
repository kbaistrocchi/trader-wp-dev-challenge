<?php get_header(); ?>

<?php
if(have_posts() ) :
    // THE WP LOOP
    while( have_posts() ) :
        the_post(); ?>
        <div>
            <h2>
                <a href="<?php the_permalink();?>">
                    <?php the_title(); ?>
                </a>
            </h2>
                <?php the_excerpt(); ?>
        </div>

    <!-- end of wp loop -->
    <?php endwhile; ?>

    <?php the_posts_navigation(); ?>

<?php else : ?>
        <p>No posts found</p>
<?php endif; ?>

<?php get_footer(); ?>