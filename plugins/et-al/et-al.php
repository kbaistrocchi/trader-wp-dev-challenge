<?php
/**
* Plugin Name: Et Al
* Description: Make sure everyone gets credit where credit is due. Add multiple authors to posts.
* Version: 1.0.0
* Author: Kayla Baistrocchi
*/

/* Run only on the post editor screen. */
add_action( 'load-post.php', 'et_al_setup' );
add_action( 'load-post-new.php', 'et_al_setup' );

/* Et Al meta box setup function */
function et_al_setup() {
    add_action( 'add_meta_boxes', 'et_al_add_post_meta_boxes' );
}

/** Create the post meta box */
function et_al_add_post_meta_boxes() {
    add_meta_box(
        'et-al-metabox',  // Unique ID
        'Developers',    // Title
        'et_al_meta_box_output',   // Callback function
        'post',         // Admin page (or post type)
        'side',         // Context
        'high'         // Priority
  );
}

/** callback function to display html of meta box */
function et_al_meta_box_output() { ?>
    <?php wp_nonce_field( basename( __FILE__ ), 'smashing_post_class_nonce' ); ?>
    <p>Additional Authors</p>
    <!-- get all users that only have the role 'author' -->
    <?php 
        $user_query = array(
            'role' => 'author',
            'orderby' => 'name'
        );
        $users = get_users( $user_query ); 
        var_dump($users);
        // display each author as a checkbox option
        foreach( $users as $user) {
            $nice_name = $user -> user_nicename;
            $name = $user -> display_name;
            $id = $user -> ID;
            ?>
            <label for="<?php echo $nice_name; ?>"><?php echo $name; ?></label>
            <input type="checkbox" name="<?php echo $nice_name; ?>" value="<?php echo $id; ?>">
            <?php
        }
    ?>
    
<?php 
}
?>