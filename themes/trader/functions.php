<?php 

// add script and stylesheets
function trader_files()
{
    wp_enqueue_style('trader_styles', get_stylesheet_uri(), null, microtime());
    // microtime forces browser to reload all info everytime instead of caching
};

add_action('wp_enqueue_scripts', 'trader_files');

// adds theme support, for thumbnails, title-tag and menus
function trader_features()
{
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    // Add theme support, (title tag, featured img, nav menu)
    register_nav_menus( array(
        'primary' => 'Primary Menu',
        'footer' => 'Footer Menu'
    ));
};
// this function dynamically loads title and tag line and is better than the header version

// after_theme_setup, function_name
add_action('after_setup_theme', 'trader_features'); 
?>