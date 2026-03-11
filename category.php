<?php get_header();
$postsPageID = get_option('page_for_posts');
$term = get_queried_object();

if ($term->parent == 0) {
	$parent_category_id = get_queried_object_id();
} else {
	$parent_category_id = $term->parent;
}

?>



<div class="gutenberg">
	<div class="wp-block-group hero-banner">
		<div class="wp-block-group__inner-container is-layout-constrained wp-block-group-is-layout-constrained" style="background-color:<?php echo $background_color; ?>">
			<h1 class="wp-block-heading has-text-align-center"><?php single_cat_title(); ?></h1>
			<p class="has-text-align-center has-large-font-size"><?php echo $term->description; ?></p>

		</div>
	</div>
</div>



<div class="container-wide">
	<div class="articles-grid">
			<div id="posts-feed">
				<?php get_template_part('template-parts/loop'); ?>

			</div>
			<?php
			//global $wp_query; // you can remove this line if everything works for you
			$total = get_queried_object()->category_count;


			// don't display the button if there are not enough posts
			if ($wp_query->max_num_pages > 1) {

				$posts_on_page = 8;
				$bar = $posts_on_page / $total * 100;

				echo '<div class="text-center">';
				echo "<div class='pagination-bar-wrapper'>";
				echo "<div class='pagination-bar-text'> Showing <span class='loaded-posts'>" . $posts_on_page . "</span> of a <span class='total-posts'>" . $total . "</span> Items </div>";
				echo "<div class='pagination-bar'><span style='width:" . $bar . "%'></span></div>";
				echo "</div>";
				echo '<div class="ajax_loadmore btn">Load more</div></div>';

			} else {
				$posts_on_page = $total;
				$bar = $posts_on_page / $total * 100;
			}
			?>
	</div>
</div>




<?php get_footer(); ?>