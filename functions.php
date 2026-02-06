<?php
/*
 *  Author: Milan Nikolic
 *  Url: https://www.nikolic.win/
 *  Custom functions, support, custom post types and more.
 */

/*------------------------------------*\
    External Modules/Files
\*------------------------------------*/
require_once(get_template_directory() . '/includes/cpt.php');
require_once(get_template_directory() . '/includes/optimize.php');
require_once(get_template_directory() . '/includes/gutenberg-extended.php');
require_once(get_template_directory() . '/includes/cf7.php');

require_once( get_template_directory() . '/includes/woo-products.php' );
//require_once( get_template_directory() . '/includes/woo-account.php' );
require_once( get_template_directory() . '/includes/woo-checkout.php' );

add_filter( 'woocommerce_enqueue_styles', '__return_false' );

/*------------------------------------*\
    Theme Support
\*------------------------------------*/

/*function mytheme_gutenberg_colors() {
    // Fetch CSS variables via PHP
    echo '<style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }
    </style>';

    // Add theme support for custom colors in Gutenberg
    add_theme_support( 'editor-color-palette', [
        [
            'name'  => __( 'Primary', 'mytheme' ),
            'slug'  => 'primary',
            'color' => 'var(--primary-color)',
        ],
        [
            'name'  => __( 'Secondary', 'mytheme' ),
            'slug'  => 'secondary',
            'color' => 'var(--secondary-color)',
        ],
        [
            'name'  => __( 'Accent', 'mytheme' ),
            'slug'  => 'accent',
            'color' => 'var(--accent-color)',
        ],
        [
            'name'  => __( 'Dark', 'mytheme' ),
            'slug'  => 'dark',
            'color' => 'var(--dark-color)',
        ],
        [
            'name'  => __( 'Light', 'mytheme' ),
            'slug'  => 'light',
            'color' => 'var(--light-color)',
        ],
    ] );
}
add_action( 'after_setup_theme', 'mytheme_gutenberg_colors' );*/

if (function_exists('add_theme_support')) {
    // Add support for editor color palette.
    add_theme_support('editor-color-palette', array(
        array(
            'name' => __('Primary', 'mytheme'),
            'slug' => 'primary',
            'color' => '#0073aa',
        ),
        array(
            'name' => __('Secondary', 'mytheme'),
            'slug' => 'secondary',
            'color' => '#005177',
        ),
        array(
            'name' => __('Accent', 'mytheme'),
            'slug' => 'accent',
            'color' => '#f78da7',
        ),
        array(
            'name' => __('Light Gray', 'mytheme'),
            'slug' => 'light-gray',
            'color' => '#f0f0f0',
        ),
        array(
            'name' => __('Dark Gray', 'mytheme'),
            'slug' => 'dark-gray',
            'color' => '#333333',
        ),
    ));
    // Add Menu Support
    add_theme_support('menus');

    add_theme_support('align-wide');

    add_theme_support('title-tag');

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');
    add_image_size('grid-item', 400, 290, true);
    //add_image_size('rectangle', 860, 430, true);
    //  add_image_size('hero', 1920, 700, true);
    // add_image_size('square', 430, 430, true);

    // Enables post and comment RSS feed links to head
    // add_theme_support('automatic-feed-links');

    add_theme_support(
        'custom-logo',
        array(
            'height' => 36,
            'width' => 220,
            'flex-width' => true,
            'flex-height' => true,
        )
    );


    // Add WooCommerce support
    add_theme_support( 'woocommerce' );

    // Add WooCommerce product gallery features (zoom, lightbox, and gallery slider)
 //   add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}






add_filter('image_size_names_choose', 'my_custom_sizes');

function my_custom_sizes($sizes)
{
    return array_merge($sizes, array(
        'grid-item' => __('Grid item'),
        'square' => __('Medium Square'),
        'rectangle' => __('Medium Rectangle')
    ));
}





function change_cover_block_thumbnail($block_content, $block)
{
    // Only apply to the Cover block
    if (isset($block['blockName']) && $block['blockName'] === 'core/cover') {
        // Get the post's featured image ID
        $featured_image_id = get_post_thumbnail_id(get_the_ID());

        if ($featured_image_id) {
            // Check if the "hero" thumbnail size exists
            $hero_thumbnail = wp_get_attachment_image_src($featured_image_id, 'hero');

            if ($hero_thumbnail) {
                // Replace the original image with the "hero" thumbnail in the block content
                $full_image_url = wp_get_attachment_url($featured_image_id);
                $block_content = str_replace($full_image_url, $hero_thumbnail[0], $block_content);
            }
        }
    }

    return $block_content;
}
add_filter('render_block', 'change_cover_block_thumbnail', 10, 2);


/*------------------------------------*\
    Enqueue scripts
\*------------------------------------*/
function theme_scripts()
{
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {

        wp_register_script('themescripts', get_template_directory_uri() . '/assets/js/general.js', array('jquery'), '1.0.0'); // Custom scripts
        wp_enqueue_script('themescripts'); // Enqueue it!


        wp_register_style('main-theme-css', get_template_directory_uri() . '/style.min.css', array(), '1.1', 'all');
        wp_enqueue_style('main-theme-css'); // Enqueue it!

        if (has_block('acf/gallery-block') || has_block('acf/posts-slider') || has_block('acf/testimonials') || is_single() ) {
            wp_register_script('swiper', get_template_directory_uri() . '/assets/js/swiper.min.js', '', '1.0.0');
            wp_enqueue_script('swiper');

            wp_register_style('swiper-css', get_template_directory_uri() . '/assets/css/swiper.min.css', array(), '1.0', 'all');
            wp_enqueue_style('swiper-css'); // Enqueue it!



        }

        if( is_front_page() || is_page() && wp_get_post_parent_id(get_the_ID()) ){
            wp_enqueue_script('custom-swiper', get_template_directory_uri() . '/assets/js/custom-swiper.js', ['swiper'], '1.0', true);
        }


        if (has_block('acf/videos') ) {
            wp_register_script('venobox', get_template_directory_uri() . '/assets/js/venobox.min.js', '', '1.0.0');
            wp_enqueue_script('venobox');

            wp_register_style('venobox-css', get_template_directory_uri() . '/assets/css/venobox.min.css', array(), '1.0', 'all');
            wp_enqueue_style('venobox-css'); // Enqueue it!
        }


        // Enqueue Lenis script from CDN with defer attribute.
        wp_enqueue_script(
            'lenis', // Handle for the script.
            'https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.10/bundled/lenis.min.js', // Script URL.
            array(), // Dependencies (none in this case).
            null, // Version (null will use the current version).
            true // Load in the footer.
        );

        // Add the defer attribute.
        add_filter('script_loader_tag', function ($tag, $handle) {
            if ('lenis' === $handle) {
                return str_replace(' src', ' defer="defer" src', $tag);
            }
            return $tag;
        }, 10, 2);


    }
}
add_action('wp_enqueue_scripts', 'theme_scripts');


/*------------------------------------*\
    CONVERT THUMBNAILS TO WEBP
\*------------------------------------*/
add_filter('image_editor_output_format', function ($formats) {
    $formats['image/jpeg'] = 'image/webp';
    $formats['image/png'] = 'image/webp';

    return $formats;
});



add_filter('upload_mimes', 'rudr_svg_upload_mimes');

function rudr_svg_upload_mimes($mimes)
{

    // it is recommended to uncomment these lines for security reasons
    // if( ! current_user_can( 'administrator' ) ) {
    // 	return $mimes;
    // }

    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';

    return $mimes;

}

add_filter('wp_check_filetype_and_ext', 'rudr_svg_filetype_ext', 10, 5);

function rudr_svg_filetype_ext($data, $file, $filename, $mimes, $real_mime)
{

    if (!$data['type']) {

        $filetype = wp_check_filetype($filename, $mimes);
        $type = $filetype['type'];
        $ext = $filetype['ext'];

        if ($type && 0 === strpos($type, 'image/') && 'svg' !== $ext) {
            $ext = false;
            $type = false;
        }

        $data = array(
            'ext' => $ext,
            'type' => $type,
            'proper_filename' => $data['proper_filename'],
        );

    }

    return $data;

}
/*------------------------------------*\
    NAVIGATION
\*------------------------------------*/
function header_nav()
{
    wp_nav_menu(
        array(
            'theme_location' => 'header-menu',
            'menu' => '',
            'container' => 'div',
            'container_class' => 'menu-{menu slug}-container',
            'container_id' => '',
            'menu_class' => 'menu',
            'menu_id' => '',
            'echo' => true,
            'fallback_cb' => 'wp_page_menu',
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul>%3$s</ul>',
            'depth' => 0,
            'walker' => ''
        )
    );
}


// Register Navigation
function register_theme_menus()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu' => __('Header Menu', 'greentheme'), // Main Navigation
        'secondary-menu' => __('Secondary Menu', 'greentheme'), // Secondary menu
        'footer-menu' => __('Footer Menu', 'greentheme'), // Footer menu
        'footer-menu2' => __('Footer Bottom Menu', 'greentheme'), // Footer bottom Navigation

    ));
}
add_action('init', 'register_theme_menus');

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation


/* * * Walker for the main menu * */
add_filter('walker_nav_menu_start_el', 'add_arrow', 10, 4);
function add_arrow($output, $item, $depth, $args)
{
    //Only add class to 'top level' items on the 'primary' menu. 
    if ('header-menu' == $args->theme_location && ($depth === 0 || $depth === 1) || 'secondary-menu' == $args->theme_location && $depth === 0) {
        if (in_array("menu-item-has-children", $item->classes)) {
            $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M19.8974 9.39739L12.3974 16.8974C12.2919 17.0027 12.1489 17.0619 11.9999 17.0619C11.8508 17.0619 11.7079 17.0027 11.6024 16.8974L4.10238 9.39739C4.00302 9.29075 3.94893 9.14972 3.9515 9.00399C3.95407 8.85827 4.01311 8.71923 4.11617 8.61617C4.21923 8.51311 4.35827 8.45407 4.50399 8.4515C4.64972 8.44893 4.79075 8.50303 4.89739 8.60239L11.9999 15.7039L19.1024 8.60239C19.209 8.50303 19.3501 8.44893 19.4958 8.4515C19.6415 8.45407 19.7805 8.51311 19.8836 8.61617C19.9867 8.71923 20.0457 8.85827 20.0483 9.00399C20.0508 9.14972 19.9967 9.29075 19.8974 9.39739Z" fill="black"/>
</svg>
';
        }
    }
    return $output;
}



/*
function add_woocommerce_categories_to_menu($items, $args) {
    if ($args->theme_location === 'header-menu') { // Adjust this to match your menu location
        $menu_items = wp_get_nav_menu_items($args->menu);

        foreach ($menu_items as $menu_item) {
            if ($menu_item->title === 'Produkte') { // Check for "Produkte"
                $product_categories = get_terms([
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => true,
                ]);

                if (!empty($product_categories)) {
                    foreach ($product_categories as $category) {
                        $items .= '<li class="menu-item menu-item-type-taxonomy menu-item-object-product_cat">';
                        $items .= '<a href="' . get_term_link($category) . '">' . esc_html($category->name) . '</a>';
                        $items .= '</li>';
                    }
                }
            }
        }
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'add_woocommerce_categories_to_menu', 10, 2);*/
class Custom_WooCommerce_Menu_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        // Output the default menu item
        parent::start_el($output, $item, $depth, $args, $id);

        // Check if the menu item title is "Produkte"
        if ($item->title == 'Produkte' && $depth == 0) {
            // Get all WooCommerce product categories
            $product_categories = get_terms(array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
            ));

            if (!empty($product_categories) && !is_wp_error($product_categories)) {
                $output .= '<ul class="sub-menu">';
                $output .= '<span class="produkte">Produkte</span><div class="wp-block-button is-style-btn-arrow">
                <a class="wp-block-button__link wp-element-button" 
                href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '">Alle Produkte anzeigen</a></div>';
                foreach ($product_categories as $category) {
                    $output .= '<li class="menu-item">';
                    $output .= '<a href="' . get_term_link($category) . '">' . $category->name . '</a>';
                    $output .= '</li>';
                }
                $output .= '</ul>';
            }
        }
    }
}


/*------------------------------------*\
    EXTEND NAV WAKLER FOR MOBILE MEGA MENU
\*------------------------------------*/
class dynamicSubMenu extends Walker_Nav_Menu
{
    function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<div class='sub-menu-wrap'><ul class='sub-menu'>\n";
    }
    function end_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul></div>\n";
    }


} //class


/*------------------------------------*\
    WIDGETS
\*------------------------------------*/
if (function_exists('register_sidebar')) {
    // Define Sidebar Widget Area 1
    register_sidebar(array(
        'name' => __('Footer top', 'greentheme'),
        'description' => __('Description for this widget-area...', 'greentheme'),
        'id' => 'widget-area-1',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}

if (function_exists('register_sidebar')) {
    // Define Sidebar Widget Area 1
    register_sidebar(array(
        'name' => __('Footer Bottom', 'greentheme'),
        'description' => __('Description for this widget-area...', 'greentheme'),
        'id' => 'widget-area-2',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}

if (function_exists('register_sidebar')) {
    // Define Sidebar Shop Sidebar
    register_sidebar(array(
        'name' => __('Shop Sidebar', 'greentheme'),
        'description' => __('Shop Sidebar with filters', 'greentheme'),
        'id' => 'widget-area-5',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}

// Add page slug to body class
function add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}
add_filter('body_class', 'add_slug_to_body_class');





/*------------------------------------*\
    COMMENTS
\*------------------------------------*/
// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style()
{
    global $wp_widget_factory;
    remove_action('wp_head', array(
        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
        'recent_comments_style'
    ));
}
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()

// Threaded Comments
function enable_threaded_comments()
{
    if (!is_admin()) {
        if (is_singular() and comments_open() and (get_option('thread_comments') == 1)) {
            wp_enqueue_script('comment-reply');
        }
    }
}

// Custom Comments Callback
function greenthemecomments($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    extract($args, EXTR_SKIP);

    if ('div' == $args['style']) {
        $tag = 'div';
        $add_below = 'comment';
    } else {
        $tag = 'li';
        $add_below = 'div-comment';
    }
    ?>
    <!-- heads up: starting < for the html tag (li or div) in the next line: -->
    <<?php echo $tag ?>     <?php comment_class(empty($args['has_children']) ? '' : 'parent') ?>
        id="comment-<?php comment_ID() ?>">
        <?php if ('div' != $args['style']): ?>
            <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
            <?php endif; ?>
            <div class="comment-author vcard">
                <?php if ($args['avatar_size'] != 0)
                    echo get_avatar($comment, $args['180']); ?>
                <?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
            </div>
            <?php if ($comment->comment_approved == '0'): ?>
                <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
                <br />
            <?php endif; ?>

            <div class="comment-meta commentmetadata"><a
                    href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)) ?>">
                    <?php
                    printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'), '  ', '');
                       ?>
            </div>

            <?php comment_text() ?>

            <div class="reply">
                <?php comment_reply_link(array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
            </div>
            <?php if ('div' != $args['style']): ?>
            </div>
        <?php endif; ?>
    <?php }



// hide admin header for everyone except the admins
if (!current_user_can('manage_options')) {
    add_filter('show_admin_bar', '__return_false');
}


/*------------------------------------*\
    Add acf options page
\*------------------------------------*/


//custom gutenberg blocks
add_action('acf/init', 'my_acf_init_block_types');
function my_acf_init_block_types()
{

    // Check function exists.
    if (function_exists('acf_register_block_type')) {

        // register logos slider block.
        acf_register_block_type(array(
            'name' => 'posts_slider',
            'title' => __('Articles slider'),
            'description' => __('Latest posts block'),
            'render_template' => 'template-parts/blocks/articles-slider.php',
            'category' => 'formatting',
            'icon' => 'admin-comments',
            'keywords' => array('Articles slider', 'sails'),
        ));
    }




}


/*------------------------------------*\
    CF7
\*------------------------------------*/





/*------------------------------------*\
    PAGINATION
\*------------------------------------*/
function greentheme_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'mid_size' => 2,
        'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M14.8334 10.2333L11.0084 6.40834C10.8523 6.25313 10.6411 6.16602 10.4209 6.16602C10.2008 6.16602 9.98955 6.25313 9.83341 6.40834C9.75531 6.48581 9.69331 6.57798 9.651 6.67953C9.6087 6.78108 9.58691 6.89 9.58691 7.00001C9.58691 7.11002 9.6087 7.21894 9.651 7.32049C9.69331 7.42204 9.75531 7.51421 9.83341 7.59168L13.6667 11.4083C13.7449 11.4858 13.8068 11.578 13.8492 11.6795C13.8915 11.7811 13.9132 11.89 13.9132 12C13.9132 12.11 13.8915 12.2189 13.8492 12.3205C13.8068 12.422 13.7449 12.5142 13.6667 12.5917L9.83341 16.4083C9.67649 16.5642 9.5879 16.7759 9.58712 16.9971C9.58633 17.2182 9.67343 17.4306 9.82925 17.5875C9.98506 17.7444 10.1968 17.833 10.418 17.8338C10.6391 17.8346 10.8515 17.7475 11.0084 17.5917L14.8334 13.7667C15.3016 13.2979 15.5645 12.6625 15.5645 12C15.5645 11.3375 15.3016 10.7021 14.8334 10.2333Z" fill="#262626"/>
</svg>',
        'next_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M14.8334 10.2333L11.0084 6.40834C10.8523 6.25313 10.6411 6.16602 10.4209 6.16602C10.2008 6.16602 9.98955 6.25313 9.83341 6.40834C9.75531 6.48581 9.69331 6.57798 9.651 6.67953C9.6087 6.78108 9.58691 6.89 9.58691 7.00001C9.58691 7.11002 9.6087 7.21894 9.651 7.32049C9.69331 7.42204 9.75531 7.51421 9.83341 7.59168L13.6667 11.4083C13.7449 11.4858 13.8068 11.578 13.8492 11.6795C13.8915 11.7811 13.9132 11.89 13.9132 12C13.9132 12.11 13.8915 12.2189 13.8492 12.3205C13.8068 12.422 13.7449 12.5142 13.6667 12.5917L9.83341 16.4083C9.67649 16.5642 9.5879 16.7759 9.58712 16.9971C9.58633 17.2182 9.67343 17.4306 9.82925 17.5875C9.98506 17.7444 10.1968 17.833 10.418 17.8338C10.6391 17.8346 10.8515 17.7475 11.0084 17.5917L14.8334 13.7667C15.3016 13.2979 15.5645 12.6625 15.5645 12C15.5645 11.3375 15.3016 10.7021 14.8334 10.2333Z" fill="#262626"/>
</svg>',
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}
add_action('init', 'greentheme_pagination');






/*** mobile picture for wp block cover ****/
add_action('enqueue_block_editor_assets', 'enqueue_responsive_cover', 100);

function enqueue_responsive_cover()
{
    $dir = get_template_directory_uri() . '/assets/js';
    wp_enqueue_script('my-cover', $dir . '/my-cover.js', ['wp-blocks', 'wp-dom'], null, true);
}


add_filter('render_block_core/cover', 'my_responsive_cover_render', 10, 2);

function my_responsive_cover_render($content, $block)
{
    // If the block has a mobile image set
    if (isset($block['attrs']['mobileImageURL'])) {
        $mobileImage = $block['attrs']['mobileImageURL'];

        // Modify the content to insert the <picture> tag with mobile <source>
        $content = preg_replace(
            '/<img([^>]+)\/>/Ui',
            "<picture><source srcset='{$mobileImage}' media='(max-width:781px)' sizes='100vw'><img$1></picture>",
            $content
        );
    }

    return $content;
}






// Disable users rest routes
/*add_filter('rest_endpoints', function( $endpoints ) {
    if ( isset( $endpoints['/wp/v2/users'] ) ) {
        unset( $endpoints['/wp/v2/users'] );
    }
    if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $endpoints;
});

*/








/**** SEO */
function noindex_paged()
{
    if (is_paged()) {
        echo '<meta name="robots" content="noindex, follow" />' . "\n";
    }
}
add_action('wp_head', 'noindex_paged');









add_filter('woocommerce_rest_check_permissions', 'allow_all_users_to_access_rest_api', 10, 4);

function allow_all_users_to_access_rest_api($permission, $context, $object_id, $post_type) {
    // Allow access to all users for all WooCommerce REST API endpoints
    return true;
}






// Add excerpt support to pages
add_action('init', 'add_excerpts_to_pages');
function add_excerpts_to_pages() {
    add_post_type_support('page', 'excerpt');
}

