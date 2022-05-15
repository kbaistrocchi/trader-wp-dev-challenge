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
    add_action('add_meta_boxes', 'et_al_add_post_meta_boxes');

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
function et_al_meta_box_output()
{
    ?>
    <?php wp_nonce_field(basename(__FILE__), 'et_al_meta_box_nonce'); ?>
    <p>Choose any additional authors that contributed to this post.</p>
    <!-- get all users that only have the role 'author' -->
    <?php 
        $user_query = array(
            'role' => 'author',
            'orderby' => 'name'
        );
        $users = get_users($user_query); 
        ?>
        <ul class="et-al-developers">
            <label>blank<input id="test-input" name="test-input" value="userID" type="checkbox"></label>
           
            <?php
            // display each author as a checkbox option
            foreach( $users as $user) {
                $nice_name = $user -> user_nicename;
                $name = $user -> display_name;
                $id = $user -> ID;
                ?>
                <li>
                    <label><input type="checkbox" name="et-al-author" value="<?php echo $id; ?>" checked ><?php echo $name; ?></label>
                </li>
                <?php
            }
            ?>
        </ul>
    
    <?php 
    echo '<pre>';
    var_dump($_POST['et-al-author']);
    echo '</pre>';
}


/** Save input from meta box */
global $post;
function et_al_save_input( $post_id, $post ) {

    /** Verify nonce */
    // if (!isset( $_POST['et_al_meta_box_nonce']) || !wp_verify_nonce(
    //     $_POST['et_al_meta_box_nonce'], basename(__FILE__)
    // ))
    // return $post_id;

    /** Get post type */
    // $post_type = get_post_type_object( $post->post_type );

    /** Check that user has permission to edit post */
    // if (!current_user_can( $post_type->cap->edit_post, $post_id ))
    // return $post_id;

    /** Get the data and clean up for html (grabs everything with the name in '') */
    
    $new_meta_value = $_POST['et-al-author'];

    /* Get the meta key. */
    $meta_key = 'additional_authors';

    /* Get the meta value of the custom field key. */
    $meta_value = get_post_meta( $post->ID, $meta_key, true );

    /* If a new meta value was added and there was no previous value, add it. */
    if ( $new_meta_value && ’ == $meta_value )
    add_post_meta( $post->ID, $meta_key, $new_meta_value, true );

    /* If the new meta value does not match the old value, update it. */
    elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post->ID, $meta_key, $new_meta_value );

    /* If there is no new meta value but an old value exists, delete it. */
    elseif ( ’ == $new_meta_value && $meta_value )
    delete_post_meta( $post->ID, $meta_key, $meta_value );
    
}

add_filter( 'the_content', 'et_al_content' );

function et_al_content( $content ) {
    $post_id = get_the_ID();
    $additional_authors = ( get_post_meta($post_id, 'additional_authors', false));

    $content .= '<div class="developer box"><p>Developers</p>';
    foreach ($additional_authors as $author ) {
        $author_info = get_userdata( $author );
        $content .= '<a href="' . $author_info->user_url . '" target="_blank">' . get_avatar($author_info->ID) . '<p>' . $author_info->display_name . '</p></a>';
    }
    $content .= '</div>';

    
    // if (!empty( $post_id )) {
    //     $devs = get_post_meta( $post_id );
    //     $dev_box = '<div>' . var_dump($devs) . '</div>';
    //     $content = $content . $dev_box;
    // }
    
    return $content;
}

?>