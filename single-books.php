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
	<section class="single-book">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 

		$id         = get_the_ID();
		$categories = get_the_terms( $id, 'genres' ); ?>

		<div class="single-book__title">
			<h1> <?php _e( the_title() ); ?> </h1>
			<p> <?php _e( the_date( 'j F Y' ) ); ?> <p>
		</div>

		<div class="single-book__categories">
			<nav>
				<?php foreach( $categories as $category ) : ?>
					<a href="<?php echo esc_url( get_term_link( $category->term_id ) ) ?>">
						<?php _e( $category->name ) ?>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>

		<div class="single-book__image">
			<figure>
				<?php wp_kses_post( the_post_thumbnail( 'medium', array( 'alt' => esc_html( get_the_title() ) ) ) ); ?>
			</figure>
		</div>
		<?php endwhile; endif; ?>
	</section>
</main><!-- #site-content -->

<?php get_footer(); ?>
