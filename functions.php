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
add_shortcode( 'custom_recent_book_shortcode', 'recent_book_shortcode' );
add_shortcode( 'custom_genres_books_shortcode', 'genres_books_shortcode' );

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

	global $post;

    if ( is_object( $post ) ){
		foreach( $terms as $term ) {
			add_rewrite_rule("library/$term/page/?([0-9]{1,})/?$", "index.php?category_name=library/$term&paged=$matches[1]", 'top');
		}
	}
}

/**
* Shortcodes
* Display recent post
*/

function recent_book_shortcode() {

	ob_start(); ?>

<div class="recent-post">
		<h2><?php _e( "Recent Post" ); ?></h2>

		<ul>

			<?php
		    $recent_posts = wp_get_recent_posts( array( 'post_type'=>'books' ) );
			$recent_post  = $recent_posts[0];
			$id           = $recent_post["ID"];
			$title        = $recent_post["post_title"]; 
			?>

			<li><a href="<?php echo get_permalink($id) ?>"><?php echo esc_html($title) ?></a></li>
		</ul> 
	</div>

	<?php return ob_get_clean();
}

/**
* Shortcodes
* Display 5 books from given post ID
*/

function genres_books_shortcode( $atts ) {

	ob_start(); 

	$attributes     = shortcode_atts( array( "id" => 5 ), $atts );
	$id             = get_term( $attributes["id"] )->term_id;
	$term_name      = term_exists( $id ) ? get_term( $id )->name : '';
	$post_type      = 'books';
	$taxonomy       = 'genres';
	$posts_per_page = 5;
	
	if ( $term_name !== '' ) :

		$args = array(
			'post_type'      => $post_type,
			'posts_per_page' => $posts_per_page,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'tax_query'      => array( array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $term_name,
			) ), 
		);

		$query = new WP_Query( $args ); ?>

	
		<h2><?php _e( "Books in category: $term_name" ); ?></h2>
		
		<?php
		if( $query->have_posts() ) { ?>
			<div class="archive-genres__list">
				<ul> 
					<?php while( $query->have_posts() ) : $query->the_post(); ?>
						<li><a href="<?php echo esc_url( the_permalink() ); ?>"><?php _e( the_title() ) ?></a></li> 
					<?php endwhile; ?>
				</ul> 
			</div>
			<?php 
			} else {
			echo "No Posts found";
		} 

	wp_reset_postdata();
	wp_reset_query();

	endif;

	return ob_get_clean();
}

/**
* DEV ENV
* development environment tests & actions
*/


// /DEV ENV/ ->

// echo do_shortcode( "[custom_genres_books_shortcode id=5]" );
flush_rewrite_rules(true);

//<- DEV ENV

