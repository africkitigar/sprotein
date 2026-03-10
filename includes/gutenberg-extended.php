<?php

/**
 * Add svg icons to facebook, linkedin and instagram in navigation menu
 */
function add_svg_to_menu_item($items, $args)
{
    // Loop through menu items
    foreach ($items as &$item) {
        // Check if the menu item has the 'facebook' class
        if (in_array('facebook', $item->classes)) {
            // Add the SVG before the menu item title
            $svg_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="19" viewBox="0 0 18 19" fill="none">
  <g clip-path="url(#clip0_1266_638)">
    <path d="M15.8906 0.5H2.10938C0.946198 0.5 0 1.4462 0 2.60938V16.3906C0 17.5538 0.946198 18.5 2.10938 18.5H15.8906C17.0538 18.5 18 17.5538 18 16.3906V2.60938C18 1.4462 17.0538 0.5 15.8906 0.5ZM16.5938 16.3906C16.5938 16.7783 16.2783 17.0938 15.8906 17.0938H11.7422V11.9609H13.9931L14.2554 9.78125H11.7422V7.42578C11.7422 6.82455 12.1956 6.37109 12.7969 6.37109H14.3789V4.33203C13.9625 4.27325 13.1593 4.19141 12.7969 4.19141C11.9769 4.19141 11.155 4.53734 10.5419 5.14035C9.91035 5.76163 9.5625 6.57544 9.5625 7.43196V9.78125H7.27734V11.9609H9.5625V17.0938H2.10938C1.72169 17.0938 1.40625 16.7783 1.40625 16.3906V2.60938C1.40625 2.22169 1.72169 1.90625 2.10938 1.90625H15.8906C16.2783 1.90625 16.5938 2.22169 16.5938 2.60938V16.3906Z" fill="#101223"/>
  </g>
  <defs>
    <clipPath id="clip0_1266_638">
      <rect width="18" height="18" fill="white" transform="translate(0 0.5)"/>
    </clipPath>
  </defs>
</svg>';
            $item->title = $svg_icon . ' ' . $item->title;
        } elseif (in_array('instagram', $item->classes)) {
            // Add the SVG before the menu item title
            $svg_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="19" viewBox="0 0 18 19" fill="none">
  <g clip-path="url(#clip0_1266_646)">
    <path d="M13.5 0.5H4.5C1.98 0.5 0 2.48 0 5V14C0 16.52 1.98 18.5 4.5 18.5H13.5C16.02 18.5 18 16.52 18 14V5C18 2.48 16.02 0.5 13.5 0.5ZM16.2 14C16.2 15.53 15.03 16.7 13.5 16.7H4.5C2.97 16.7 1.8 15.53 1.8 14V5C1.8 3.47 2.97 2.3 4.5 2.3H13.5C15.03 2.3 16.2 3.47 16.2 5V14Z" fill="#101223"/>
    <path d="M9 5C6.48 5 4.5 6.98 4.5 9.5C4.5 12.02 6.48 14 9 14C11.52 14 13.5 12.02 13.5 9.5C13.5 6.98 11.52 5 9 5ZM9 12.2C7.47 12.2 6.3 11.03 6.3 9.5C6.3 7.97 7.47 6.8 9 6.8C10.53 6.8 11.7 7.97 11.7 9.5C11.7 11.03 10.53 12.2 9 12.2Z" fill="#101223"/>
    <path d="M13.4996 5.89961C13.9967 5.89961 14.3996 5.49667 14.3996 4.99961C14.3996 4.50255 13.9967 4.09961 13.4996 4.09961C13.0026 4.09961 12.5996 4.50255 12.5996 4.99961C12.5996 5.49667 13.0026 5.89961 13.4996 5.89961Z" fill="#101223"/>
  </g>
  <defs>
    <clipPath id="clip0_1266_646">
      <rect width="18" height="18" fill="white" transform="translate(0 0.5)"/>
    </clipPath>
  </defs>
</svg>';
            $item->title = $svg_icon . ' ' . $item->title;
        } elseif (in_array('linkedin', $item->classes)) {
            // Add the SVG before the menu item title
            $svg_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="19" viewBox="0 0 18 19" fill="none">
  <g clip-path="url(#clip0_1266_642)">
    <path d="M5.62527 15.3711H3.16434V7.42578H5.62527V15.3711ZM5.87082 4.89439C5.87082 4.09885 5.22537 3.45312 4.43024 3.45312C3.63208 3.45312 2.98828 4.09885 2.98828 4.89439C2.98828 5.69022 3.63208 6.33594 4.43024 6.33594C5.22537 6.33594 5.87082 5.69022 5.87082 4.89439ZM14.8359 10.9999C14.8359 8.86705 14.3854 7.28516 11.8938 7.28516C10.6966 7.28516 9.89291 7.88391 9.56483 8.50656H9.5625V7.42578H7.17188V15.3711H9.5625V11.4262C9.5625 10.3931 9.82549 9.3922 11.1061 9.3922C12.3692 9.3922 12.4102 10.5736 12.4102 11.4918V15.3711H14.8359V10.9999ZM18 16.3906V2.60938C18 1.4462 17.0538 0.5 15.8906 0.5H2.10938C0.946198 0.5 0 1.4462 0 2.60938V16.3906C0 17.5538 0.946198 18.5 2.10938 18.5H15.8906C17.0538 18.5 18 17.5538 18 16.3906ZM15.8906 1.90625C16.2783 1.90625 16.5938 2.22169 16.5938 2.60938V16.3906C16.5938 16.7783 16.2783 17.0938 15.8906 17.0938H2.10938C1.72169 17.0938 1.40625 16.7783 1.40625 16.3906V2.60938C1.40625 2.22169 1.72169 1.90625 2.10938 1.90625H15.8906Z" fill="#101223"/>
  </g>
  <defs>
    <clipPath id="clip0_1266_642">
      <rect width="18" height="18" fill="white" transform="translate(0 0.5)"/>
    </clipPath>
  </defs>
</svg>';
            $item->title = $svg_icon . ' ' . $item->title;
        }
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'add_svg_to_menu_item', 10, 2);




/**
 * Remove wp-block-group__inner-container div for nested wp-block-group
 */
function remove_inner_container_for_nested_groups($block_content, $block) {
    if ($block['blockName'] === 'core/group' && !empty($block['innerBlocks'])) {
        // Remove the inner container for nested groups
        $block_content = preg_replace('/<div class="wp-block-group__inner-container">(.*?)<\/div>/', '$1', $block_content);
    }
    return $block_content;
}
add_filter('render_block', 'remove_inner_container_for_nested_groups', 10, 2);




/**
 * Extend and add new button types
 */
add_action('init', 'my_register_cover_styles');
function my_register_cover_styles()
{
    register_block_style('core/button', [
        'name' => 'btn-outline-white',
        'label' => 'White outline button',
    ]);
    register_block_style('core/button', [
        'name' => 'btn-arrow',
        'label' => 'Button with arrow',
    ]);
    register_block_style('core/button', [
        'name' => 'btn-white',
        'label' => 'White Background Button',
    ]);

}



/**
 * Customize social icons from gutenberg block
 */
add_filter(
    'render_block',
    function ($block_content, $block) {
        if ('core/social-link' === $block['blockName'] && 'youtube' === $block['attrs']['service']) {
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
  <circle cx="20" cy="20" r="20" fill="#A6CE39"/>
  <path d="M28 17.5C28 15.6 26.4 14 24.5 14H15.5C13.6 14 12 15.6 12 17.5V21.7C12 23.6 13.6 25.2 15.5 25.2H24.5C26.4 25.2 28 23.6 28 21.7V17.5ZM22.7 19.9L18.7 21.9C18.5 22 18 21.9 18 21.7V17.6C18 17.4 18.5 17.3 18.7 17.4L22.6 19.5C22.7 19.6 22.8 19.8 22.7 19.9Z" fill="white"/>
</svg>';
            $before = explode('<svg', $block_content);
            $after = explode('</svg>', $before[1]);
            $block_content = $before[0] . $icon . $after[1];
        } elseif ('core/social-link' === $block['blockName'] && 'linkedin' === $block['attrs']['service']) {
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
  <circle cx="20" cy="20" r="20" fill="#A6CE39"/>
  <path d="M12.2336 16.2555H15.5036V26.8832H12.2336V16.2555ZM13.8686 11C14.9197 11 15.7372 11.8175 15.7372 12.8686C15.7372 13.9197 14.9197 14.7372 13.8686 14.7372C12.8175 14.854 12 13.9197 12 12.8686C12 11.8175 12.8175 11 13.8686 11Z" fill="white"/>
  <path d="M17.606 16.2551H20.7592V17.6565C21.3432 16.839 22.3943 16.0215 24.0293 16.0215C27.4161 16.0215 28.0001 18.2405 28.0001 21.0434V26.8828H24.73V21.7441C24.73 20.4594 24.73 18.9412 22.9782 18.9412C21.2264 18.9412 20.9928 20.2259 20.9928 21.6273V26.8828H17.606V16.2551Z" fill="white"/>
</svg>
            ';
            $before = explode('<svg', $block_content);
            $after = explode('</svg>', $before[1]);
            $block_content = $before[0] . $icon . $after[1];
        } elseif ('core/social-link' === $block['blockName'] && 'instagram' === $block['attrs']['service']) {
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
  <circle cx="20" cy="20" r="20" fill="#A6CE39"/>
  <path d="M24 14C23.4 14 23 14.4 23 15C23 15.6 23.5 16 24 16C24.6 16 25 15.6 25 15C25 14.4 24.6 14 24 14Z" fill="white"/>
  <path d="M20.2 16C17.9 16 16 17.9 16 20.2C16 22.5 17.9 24.4 20.2 24.4C22.5 24.4 24.4 22.5 24.4 20.2C24.4 17.9 22.5 16 20.2 16ZM20.2 22.9C18.7 22.9 17.5 21.7 17.5 20.2C17.5 18.7 18.7 17.5 20.2 17.5C21.7 17.5 22.9 18.7 22.9 20.2C22.9 21.7 21.7 22.9 20.2 22.9Z" fill="white"/>
  <path d="M23.5977 29H16.399C13.4348 29 11 26.5789 11 23.6316V16.3684C11 13.4211 13.4348 11 16.399 11H23.5977C26.5618 11 28.9967 13.4211 28.9967 16.3684V23.5263C29.1025 26.5789 26.6677 29 23.5977 29ZM16.399 12.6842C14.2817 12.6842 12.6938 14.2632 12.6938 16.2632V23.4211C12.6938 25.5263 14.3876 27.1053 16.399 27.1053H23.5977C25.7149 27.1053 27.3029 25.4211 27.3029 23.4211V16.3684C27.3029 14.2632 25.6091 12.6842 23.5977 12.6842H16.399Z" fill="white"/>
</svg>
            ';
            $before = explode('<svg', $block_content);
            $after = explode('</svg>', $before[1]);
            $block_content = $before[0] . $icon . $after[1];
        } elseif ('core/social-link' === $block['blockName'] && 'facebook' === $block['attrs']['service']) {
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
  <circle cx="20" cy="20" r="20" fill="#A6CE39"/>
  <path d="M24.2903 20.7064L24.8226 16.9532H21.2742V14.5404C21.2742 13.4681 21.8065 12.4851 23.4032 12.4851H25V9.26809C25 9.26809 23.4919 9 22.0726 9C19.2339 9 17.2823 10.7872 17.2823 14.0936V16.9532H14V20.7064H17.2823V29.8213C17.9032 29.9106 18.6129 30 19.3226 30C20.0323 30 20.6532 29.9106 21.3629 29.8213V20.7064H24.2903Z" fill="white"/>
</svg>
            ';
            $before = explode('<svg', $block_content);
            $after = explode('</svg>', $before[1]);
            $block_content = $before[0] . $icon . $after[1]; 
        } 
        return $block_content;
    },
    10,
    2
);





/**
 * Summary of customize_gallery_markup
 * Gutenberg gallery with 1 column turn into swiper slider
 */
function customize_gallery_markup($block_content, $block) {
    // Check if the block is a gallery block
    if ('core/gallery' === $block['blockName']) {
        // Get gallery attributes
        $attributes = isset($block['attrs']) ? $block['attrs'] : [];

        // Check if the 'columns' attribute exists and is set to 1
        if (isset($attributes['columns']) && (int) $attributes['columns'] === 1) {
            // Transform gallery output into Swiper-compatible structure

            // Wrap each image in a Swiper slide
            $block_content = preg_replace(
                '/<figure class="wp-block-image(.*?)">/',
                '<div class="swiper-slide"><figure class="wp-block-image$1">',
                $block_content
            );

            // Close the swiper-slide div after each figure
            $block_content = str_replace('</figure>', '</figure></div>', $block_content);

            // Remove the gallery wrapper <figure>
            $block_content = preg_replace(
                '/<figure class="wp-block-gallery(.*?)">|<\/figure>/',
                '',
                $block_content
            );

            // Wrap the resulting slides in Swiper structure and add navigation arrows
            $block_content = '
                <figure class="swiper-gallery">
                    <div class="swiper-wrapper">' . $block_content . '
                    
                </figure>';
        }
    }

    return $block_content;
}
add_filter('render_block', 'customize_gallery_markup', 10, 2);







