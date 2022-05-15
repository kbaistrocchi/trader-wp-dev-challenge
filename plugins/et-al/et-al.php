<?php
/**
 * Plugin Name: Et Al
 * Description: Make sure everyone gets credit where credit is due. Add multiple authors to posts.
 * Version: 1.0.0
 * Author: Kayla Baistrocchi
 */

/* Run only on the post editor screen. */
add_action('load-post.php', 'et_al_setup');
add_action('load-post-new.php', 'et_al_setup');

/* Et Al meta box setup function */
function et_al_setup()
{
    /** Add meta box using hook */
    add_action('add_meta_boxes_post', 'et_al_add_post_meta_boxes');

    /** Save meta box input when post is saved */
    add_action('save_post', 'et_al_save_input', 10, 2);
}

/**
 * Create the post meta box 
 */

function et_al_add_post_meta_boxes()
{
    add_meta_box(
        'et-al-metabox',  // Unique ID
        'Developers',    // Title
        'et_al_meta_box_output',   // Callback function
        'post',         // Admin page (or post type)
        'side',         // Context
        'core'         // Priority
    );
}

/**
 * callback function to display html of meta box 
 */
function et_al_meta_box_output($post)
{
    ?>
    <?php wp_nonce_field(basename(__FILE__), 'et_al_meta_box_nonce'); ?>
    <p>Choose any additional authors that contributed to this post.</p>
    <!-- get all users that only have the role 'author' -->
    <?php 
        /** To DO - if stored data exists, first create elements with the stored data */
        // $stored_additional_authors = get_post_meta( $post->ID, 'additional_authors')[0];
        // echo '<pre>';
        // var_dump($stored_additional_authors);
        // echo '</pre>';
        /** Get all users that have 'author' access only */
        $user_query = array(
            'role' => 'author',
            'orderby' => 'name'
        );
        $users = get_users($user_query); 
        ?>
        <ul class="et-al-developers">
            <?php
            // display each author as a checkbox option
            foreach( $users as $user) {
                ?>
                <li>
                    <label>
                        <input type="checkbox" name="et-al-author[<?php $user->ID ?>]" value="<?php echo $user->ID; ?>" >
                        <?php echo $user->display_name; ?>
                    </label>
                </li>
                <?php
            }
            ?>
        </ul>
    
    <?php 
}


/** Save input from meta box */
function et_al_save_input( $post ) {
    global $post;

    /** Verify nonce */
    // if (!isset( $_POST['et_al_meta_box_nonce']) || !wp_verify_nonce(
    //     $_POST['et_al_meta_box_nonce'], basename(__FILE__)
    // ))
    // return $post_id;

    /** Get the data and clean up for html (grabs checked boxes with the name="et-al-author") */
    $new_meta_value = $_POST['et-al-author'];

    /* Get the meta key. */
    $meta_key = 'additional_authors';

    /* Get the meta value of the custom field key. */
    $meta_value = get_post_meta( $post->ID, $meta_key, true );

    /* Updated meta data with checked box values */
    update_post_meta( $post->ID, $meta_key, $new_meta_value );
    
}

add_filter( 'the_content', 'et_al_content' );

function et_al_content( $content ) {
    $post_id = get_the_ID();
    $additional_authors = ( get_post_meta($post_id, 'additional_authors', false));
    // echo '<pre>';
    // echo var_dump($additional_authors[0]);
    // echo '</pre>';

    $content .= '<div class="developer box"><p>Developers</p>';
    foreach ($additional_authors[0] as $author ) {
        $author_info = get_userdata( $author );
        $content .= '<a href="' . $author_info->user_url . '" target="_blank">' . get_avatar($author_info->ID) . '<p>' . $author_info->display_name . '</p></a>';
    }
    $content .= '</div>';
    
    return $content;
}

?>