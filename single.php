<?php get_header();
$postsPageID = get_option('page_for_posts');
$author_id = get_post_field('post_author', get_the_id());
//$author_image = get_field('author_image', 'user_' . $author_id);
$author_id = get_post_field('post_author', get_the_id());
$author_name = get_the_author_meta('display_name', $author_id);
//$author_img_id = get_field('author_image', 'user_' . $author_id);
$author_bio = get_the_author_meta('description', $author_id);
$author_link = get_author_posts_url($author_id);
?>

<div class="single-post-intro">
	<div class="container">
		
		<?php
		// Check if the current post has categories
		$categories = get_the_category();

		if (!empty($categories)) {
			$parent_category = null;

			foreach ($categories as $category) {
				// Check if the category is a parent category (parent ID = 0)
				if ($category->parent == 0) {
					$parent_category = $category;
					break; // Stop the loop when we find a parent category
				}
			}

			// If we found a parent category, display its link
			/*if ($parent_category) {
				$parent_link = get_category_link($parent_category->term_id);
				echo '<a class="read-article" href="' . esc_url($parent_link) . '">' . esc_html($parent_category->name) . '</a>';
			}*/
		}
		?>
		<div class="breadcrumbs">
			<a href="<?php echo get_home_url(); ?>">Početna</a>
			<?php if ($parent_category) {
				$parent_link = get_category_link($parent_category->term_id);
				echo '<a href="' . esc_url($parent_link) . '">' . esc_html($parent_category->name) . '</a>';
			}?>
		</div>

		<h1><?php echo get_the_title(); ?></h1>

		<span class="date"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M216 64C229.3 64 240 74.7 240 88L240 128L400 128L400 88C400 74.7 410.7 64 424 64C437.3 64 448 74.7 448 88L448 128L480 128C515.3 128 544 156.7 544 192L544 480C544 515.3 515.3 544 480 544L160 544C124.7 544 96 515.3 96 480L96 192C96 156.7 124.7 128 160 128L192 128L192 88C192 74.7 202.7 64 216 64zM216 176L160 176C151.2 176 144 183.2 144 192L144 240L496 240L496 192C496 183.2 488.8 176 480 176L216 176zM144 288L144 480C144 488.8 151.2 496 160 496L480 496C488.8 496 496 488.8 496 480L496 288L144 288z"/></svg><?php the_time('d. F Y.'); ?></span>

		<?php if (has_post_thumbnail()):
			echo $featured_image = get_the_post_thumbnail(get_the_ID(), 'hero');
		?>

		<?php endif; ?>
	</div> <!-- /container -->
</div><!-- /single-post-intro -->

<div class="gutenberg">



	<div class="container-narrow">
		<?php
		if (!has_excerpt()) {

		} else {
			echo '<div class="single-excerpt">' . get_the_excerpt() . '</div>';
		}
		?>
		<?php the_content(); // Dynamic Content ?>
	</div>



</div> <!-- /gutenberg -->



<?php get_template_part('template-parts/related-posts'); ?>


<?php get_footer(); ?>