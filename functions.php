<?php
/**
* Twentytwenty-child Theme functions and definitions.
*
* @link https://developer.wordpress.org/themes/basics/theme-functions/
*
* @package twentytwenty-child
*/

/**
* Actions & filters
*/

add_action( "wp_enqueue_scripts", "twentytwenty_parent_theme_enqueue_styles" );
add_action( "wp_enqueue_scripts", "jquery_footer_scripts" );
add_action( "init", "books_post_type" );
add_action( "init", "books_post_taxonomies" );
add_filter( 'post_type_link', 'books_link_rewrite', 1, 3 );
add_action('init', 'pagination_rewrite');

/**
* Enqueue scripts and styles
*/

function twentytwenty_parent_theme_enqueue_styles() {
	wp_enqueue_style( 
		"twentytwenty-style",
		get_template_directory_uri() . "/style.css"
	);
	wp_enqueue_style(
		"twentytwenty-child-style",
		get_stylesheet_directory_uri() . "/style.css",
		[ "twentytwenty-style" ]
	);
}

function jquery_footer_scripts() {
    wp_enqueue_script(
        "jquery-script",
        get_stylesheet_directory_uri() . "/assets/js/scripts.js",
        array( "jquery" ),
		"1.0.0",
		true
    );
}

/**
*  Custom Post Type
*  Template for labels
*/

function labels_template(string $plural, string $singular, string $reference) {
	return array(
		"name"                => _x( "$plural", "$reference General Name", "twentytwenty" ),
		"singular_name"       => _x( "$singular", "$reference Singular Name", "twentytwenty" ),
		"all_items"           => _x( "All $plural", "$reference All Items Name", "twentytwenty" ),
        "view_item"           => _x( "View $singular", "$reference View Item Name", "twentytwenty" ),
        "add_new_item"        => _x( "Add New $singular", "$reference Add New Item Name", "twentytwenty" ),
        "add_new"             => _x( "Add New $singular", "$reference Add New", "twentytwenty" ),
        "edit_item"           => _x( "Edit $singular", "$reference Edit Item Name", "twentytwenty" ),
        "update_item"         => _x( "Update $singular", "$reference Update Item Name", "twentytwenty" ),
        "search_items"        => _x( "Search $singular", "$reference Search Items Name", "twentytwenty" ),
        "not_found"           => _x( "$singular Not Found", "$reference Not Found Name", "twentytwenty" ),
        "not_found_in_trash"  => _x( "$singular Not found in Trash", "$reference Not Found In Trash Name", "twentytwenty" ),
    );
}

/**
*  Custom Post Type
*  Register 'Books' CPT
*/

function books_post_type() {

	$supports = array(
		"title",
		"editor",
		"thumbnail"
	);

	$args = array(
		"labels"       => labels_template("Books", "Book", "Books Post Type"),
		"hierarchical"       => false,
		"public"             => true,
		"has_archive"        => true,
		"rewrite"            => array('slug' => 'library/%genres%'),
		'publicly_queryable' => true,
		'taxonomies'         => array('genres'),
		"menu_icon"          => "dashicons-book",
		"supports"           => $supports,
		'capability_type'    => 'post',
	);

	register_post_type( "books", $args );
}

/**
*  Custom Post Type
*  Register 'Genres' taxonomy for 'Books' CPT
*/

function books_post_taxonomies() {
	$args = array(
		"labels"            => labels_template("Genres", "Genre", "Books Post Type Taxonomy"),
		"hierarchical"      => true, //true - category, false - tag
		"public"            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
        'show_in_nav_menus' => true,
		"rewrite"           => true
	);

	register_taxonomy( "genres", array( "books" ), $args );
}

/**
*  Custom Post Type
*  Add rewrite rule for 'Books' single page to display /post/term/post-name link structure
*/

function books_link_rewrite( $post_link, $id = 0 ){

    $post = get_post($id);  

    if ( is_object( $post ) ){

        $terms = wp_get_object_terms( $post->ID, 'genres' );

        if( $terms ){

            return str_replace( '%genres%' , $terms[0]->slug , $post_link );
        }
    }
    return $post_link;  
}

/**
*  Custom Post Type
*  Add rewrite rule for 'Genres' archive pagination links
*/

function pagination_rewrite() {

	$terms = get_terms([
		'taxonomy' => 'genres',
		'hide_empty' => false,
	]);

    if ( is_object( $post ) ){
		foreach( $terms as $term ) {
			add_rewrite_rule("library/$term/page/?([0-9]{1,})/?$", "index.php?category_name=library/$term&paged=$matches[1]", 'top');
		}
	}
}

/**
* Flush rewrite rules
* For development environment
*/

//DEV ENV ->
flush_rewrite_rules(true);
//<- DEV ENV

