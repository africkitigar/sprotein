<?php if (have_posts()):
	$i = 0;
	while (have_posts()):
		the_post();
		$i++; ?>


		<article class="post-in-loop">
			<?php if (has_post_thumbnail()): ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="post-in-loop-image">
					<?php the_post_thumbnail('grid-item'); ?>
				</a>
			<?php endif; ?>


			<div class="post-in-loop-content">
				<h2>
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
				</h2>

				<div class="post-excerpt">
					<?php echo get_the_excerpt(); ?>
				</div>
				<a class="read-article btn" href="<?php echo get_the_permalink(); ?>">Pročitaj post</a>
			</div>



		</article>


	<?php endwhile; ?>

<?php endif; ?>