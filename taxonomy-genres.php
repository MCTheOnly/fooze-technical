<?php
/**
 * The template for displaying single book pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package twentytwenty-child
 */

get_header();

?>

<main id="site-content">
	<section class="archive-genres">
	<?php

		$post_type      = 'books';
		$taxonomy       = 'genres';
		$term           = $wp_query->get_queried_object()->name;
		$paged          = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$posts_per_page = 5;

		$args = array(
			'post_type'      => $post_type,
			'posts_per_page' => $posts_per_page,
			'order'          => 'ASC',
			'paged'          => $paged,	
			'tax_query'      => array( array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $term,
				),
			),
		);

		$query = new WP_Query( $args );

		if( $query->have_posts() ) : ?>

		<div class="archive-genres__list">
			<ul> 
				<?php while( $query->have_posts() ) : $query->the_post(); ?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></li> 
				<?php endwhile; ?>
			</ul> 
		</div>
		<?php 
		wp_reset_postdata();
		wp_reset_query();
		endif; ?>

		<div class="archive-genres__pagination">
			<div class="archive-genres__container">
		    	<?php 
		        echo paginate_links( array(
		            'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
		            'total'        => $query->max_num_pages,
		            'current'      => max( 1, get_query_var( 'paged' ) ),
		            'format'       => '?paged=%#%',
		            'show_all'     => false,
		            'type'         => 'plain',
		            'end_size'     => 10,
		            'mid_size'     => 1,
		            'prev_next'    => true,
		            'prev_text'    => sprintf( '<i></i> %1$s', __( 'Newer Posts', 'text-domain' ) ),
		            'next_text'    => sprintf( '%1$s <i></i>', __( 'Older Posts', 'text-domain' ) ),
		            'add_args'     => false,
		            'add_fragment' => '',
		        ) ); ?>
			</div>
		</div>

		
	</section>
</main><!-- #site-content -->

<?php get_footer(); ?>
