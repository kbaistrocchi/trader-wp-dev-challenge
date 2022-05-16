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
        'et-al-metabox', 
        'Developers',    
        'et_al_meta_box_output',   // Callback function
        'post',         
        'side',         
        'core'         
    );
}

/**
 * callback function to display html of meta box in editor
 */
function et_al_meta_box_output($post)
{
    global $post;
    ?>
    <?php wp_nonce_field(basename(__FILE__), 'et_al_meta_box_nonce'); ?>
    <p>Choose any additional authors that contributed to this post.</p>
    <?php 
        /** Get all users that have 'author' access only */
        $user_query = array(
            'role' => 'author',
            'orderby' => 'name'
        );
        $users = get_users($user_query); 

        /** Get any additional authors already saved for this post */
        $already_added = get_post_meta( $post->ID, 'additional_authors')[0];
        /** Helper function to check if user has already been added to post */
        function checkUser($userID, $array) {
            if (in_array( $userID, $array)) {
                return $userID;
            } else {
                return 'not there';
            }
        }
        ?>
        <ul class="et-al-developers">
            <?php
            // display each author as a checkbox option
            foreach( $users as $user) {
                /** Get user id if they've already been added to this post previously
                 * If they have, then their checkbox will be marked as checked
                 */
                $existing = checkUser($user->ID, $already_added);
    
                ?>
                <li>
                    <label>
                        <input type="checkbox" name="et-al-author[<?php $user->ID ?>]" value="<?php echo $user->ID; ?>" <?php checked($existing, $user->ID); ?> />
                        <?php echo $user->display_name; ?>
                    </label>
                </li>
                <?php
            }
            ?>
        </ul>
    
    <?php 
}


/** Save input from meta box when post is saved */
function et_al_save_input( $post ) {
    global $post;

    /** Verify nonce */
    if (!isset( $_POST['et_al_meta_box_nonce']) || !wp_verify_nonce(
        $_POST['et_al_meta_box_nonce'], basename(__FILE__)
    ))
    return $post_id;

    /** Get the data (grabs checked boxes with the name="et-al-author") */
    $new_meta_value = $_POST['et-al-author'];

    /* Get the meta key. */
    $meta_key = 'additional_authors';

    /* Updated meta data with checked box values */
    update_post_meta( $post->ID, $meta_key, $new_meta_value );
    
}

/** Add meta data to the post content */
add_filter( 'the_content', 'et_al_content' );

function et_al_content( $content ) {
    $post_id = get_the_ID();
    $additional_authors = ( get_post_meta($post_id, 'additional_authors', false));
    if ( count($additional_authors[0]) > 0 ) {
        $content .= '<div class="developer-box"><p>Developers</p>';
        foreach ($additional_authors[0] as $author ) {
            $author_info = get_userdata( $author );
            $content .= '<div><a href="' .  get_author_posts_url($author_info->ID) . '" target="_blank">' . get_avatar($author_info->ID) . '<p>' . $author_info->display_name . '</p></a></div>';
        }
        $content .= '</div>';
        
        return $content;
    }
    
}

?>