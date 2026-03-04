<?php
/**
 * Summary of home_blog_posts_shortcode
 * [home_blog_posts posts="3"]
 */
function home_blog_posts_shortcode($atts)
{

    $atts = shortcode_atts(array(
        'posts' => 3,
    ), $atts);

    ob_start();

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => intval($atts['posts']),
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
        'no_found_rows' => true,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()):

        echo '<div class="home-blog-posts" id="posts-feed">';

        while ($query->have_posts()):
            $query->the_post();
            ?>
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
            <?php
        endwhile;

        echo '</div>';

    endif;

    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('home_blog_posts', 'home_blog_posts_shortcode');