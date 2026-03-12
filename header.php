<!doctype html>
<html <?php language_attributes(); ?> class="no-js">

<head>
	<meta charset="<?php bloginfo('charset'); ?>">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link
		href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
		rel="stylesheet">

</head>

<body <?php body_class(); ?>>

	<!-- wrapper -->
	<div class="wrapper">

		<!-- header -->
		<header class="header" role="banner">


			<div class="flex container">
				<?php get_template_part('template-parts/header-promo'); ?>

				<div class="socials">
					<a href="https://www.facebook.com/Super.Protein.Olympic" target="_blank">
						<svg fill="#fff" width="24px" height="24px" viewBox="0 0 32 32"
							xmlns="http://www.w3.org/2000/svg">
							<path
								d="M21.95 5.005l-3.306-.004c-3.206 0-5.277 2.124-5.277 5.415v2.495H10.05v4.515h3.317l-.004 9.575h4.641l.004-9.575h3.806l-.003-4.514h-3.803v-2.117c0-1.018.241-1.533 1.566-1.533l2.366-.001.01-4.256z" />
						</svg>
					</a>
					<a href="https://www.instagram.com/superprotein.rs" target="_blank">
						<svg width="24px" height="24px" viewBox="0 0 24 24" fill="none"
							xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd"
								d="M2 6C2 3.79086 3.79086 2 6 2H18C20.2091 2 22 3.79086 22 6V18C22 20.2091 20.2091 22 18 22H6C3.79086 22 2 20.2091 2 18V6ZM6 4C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20H18C19.1046 20 20 19.1046 20 18V6C20 4.89543 19.1046 4 18 4H6ZM12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9ZM7 12C7 9.23858 9.23858 7 12 7C14.7614 7 17 9.23858 17 12C17 14.7614 14.7614 17 12 17C9.23858 17 7 14.7614 7 12ZM17.5 8C18.3284 8 19 7.32843 19 6.5C19 5.67157 18.3284 5 17.5 5C16.6716 5 16 5.67157 16 6.5C16 7.32843 16.6716 8 17.5 8Z"
								fill="#fff" />
						</svg>
					</a>
				</div>


			</div>


		</header>
		<!-- /header -->

		<div class="body-content">

			<div class="header-bottom">
				<div class="container flex flex-vertical-center">


					<!-- logo -->
					<div class="logo">
						<?php if (function_exists('the_custom_logo')) {
							the_custom_logo();
						} ?>
					</div>
					<!-- /logo -->


					<!-- nav -->
					<nav class="nav flex" role="navigation">
						<?php header_nav(); ?>

						<div class="mobile-menu-footer">
							<?php if (has_nav_menu('secondary-menu')) {
								wp_nav_menu(array('theme_location' => 'secondary-menu'));
							}
							?>
						</div>
					</nav>
					<!-- /nav -->


					<div id="mob-menu-bar">
						<div class="bar1"></div>
						<div class="bar2"></div>
						<div class="bar3"></div>
					</div>

					<button id="search-toggle" class="header-icon search-icon" aria-label="Pretraga">
						<svg width="27px" height="27px" viewBox="0 -0.5 25 25" fill="none"
							xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd"
								d="M5.5 10.7655C5.50003 8.01511 7.44296 5.64777 10.1405 5.1113C12.8381 4.57483 15.539 6.01866 16.5913 8.55977C17.6437 11.1009 16.7544 14.0315 14.4674 15.5593C12.1804 17.0871 9.13257 16.7866 7.188 14.8415C6.10716 13.7604 5.49998 12.2942 5.5 10.7655Z"
								stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
							</path>
							<path d="M17.029 16.5295L19.5 19.0005" stroke="#000000" stroke-width="1.5"
								stroke-linecap="round" stroke-linejoin="round"></path>
						</svg>
					</button>

					<?php if (class_exists('WooCommerce')): ?>
						<a class="cart-contents-wrap" href="<?php echo wc_get_cart_url(); ?>" title="Idi na plaćanje">
							<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
								<path
									d="M13 27C13 27.3956 12.8827 27.7822 12.6629 28.1111C12.4432 28.44 12.1308 28.6964 11.7654 28.8478C11.3999 28.9991 10.9978 29.0387 10.6098 28.9616C10.2219 28.8844 9.86549 28.6939 9.58579 28.4142C9.30608 28.1345 9.1156 27.7781 9.03843 27.3902C8.96126 27.0022 9.00087 26.6001 9.15224 26.2346C9.30362 25.8692 9.55996 25.5568 9.88886 25.3371C10.2178 25.1173 10.6044 25 11 25C11.5304 25 12.0391 25.2107 12.4142 25.5858C12.7893 25.9609 13 26.4696 13 27ZM24 25C23.6044 25 23.2178 25.1173 22.8889 25.3371C22.56 25.5568 22.3036 25.8692 22.1522 26.2346C22.0009 26.6001 21.9613 27.0022 22.0384 27.3902C22.1156 27.7781 22.3061 28.1345 22.5858 28.4142C22.8655 28.6939 23.2219 28.8844 23.6098 28.9616C23.9978 29.0387 24.3999 28.9991 24.7654 28.8478C25.1308 28.6964 25.4432 28.44 25.6629 28.1111C25.8827 27.7822 26 27.3956 26 27C26 26.4696 25.7893 25.9609 25.4142 25.5858C25.0391 25.2107 24.5304 25 24 25ZM29.9638 9.2675L26.7588 20.8025C26.5825 21.4326 26.2056 21.9881 25.6852 22.3846C25.1648 22.7812 24.5293 22.9973 23.875 23H11.52C10.8638 22.9997 10.2257 22.7848 9.703 22.388C9.18031 21.9913 8.80173 21.4345 8.625 20.8025L4.24 5H2C1.73478 5 1.48043 4.89464 1.29289 4.70711C1.10536 4.51957 1 4.26522 1 4C1 3.73478 1.10536 3.48043 1.29289 3.29289C1.48043 3.10536 1.73478 3 2 3H5C5.21863 2.99996 5.43124 3.07156 5.6053 3.20386C5.77936 3.33615 5.90527 3.52184 5.96375 3.7325L7.14875 8H29C29.1542 7.99997 29.3062 8.03558 29.4444 8.10406C29.5825 8.17254 29.7029 8.27202 29.7962 8.39474C29.8895 8.51746 29.9532 8.66009 29.9823 8.81149C30.0113 8.96289 30.005 9.11895 29.9638 9.2675ZM27.6838 10H7.705L10.5562 20.2675C10.6147 20.4782 10.7406 20.6638 10.9147 20.7961C11.0888 20.9284 11.3014 21 11.52 21H23.875C24.0936 21 24.3062 20.9284 24.4803 20.7961C24.6544 20.6638 24.7803 20.4782 24.8388 20.2675L27.6838 10Z"
									fill="#000" />
							</svg>
							<span class="cart-count cart-contents">
								<?php echo WC()->cart->get_cart_contents_count(); ?></span>
						</a>
					<?php endif; ?>
				</div><!-- /container -->

				<div id="header-search-bar" class="header-search-bar">
					<div class="header-search-bar-inner">
					<form role="search" method="get" class="woocommerce-product-search"
						action="<?php echo esc_url(home_url('/')); ?>">

						<input type="search" class="search-field" placeholder="Pretraži proizvode…"
							value="<?php echo get_search_query(); ?>" name="s" />
						<input type="hidden" name="post_type" value="product" />
						<button type="submit" class="search-submit"><svg width="27px" height="27px" viewBox="0 -0.5 25 25"
								fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd"
									d="M5.5 10.7655C5.50003 8.01511 7.44296 5.64777 10.1405 5.1113C12.8381 4.57483 15.539 6.01866 16.5913 8.55977C17.6437 11.1009 16.7544 14.0315 14.4674 15.5593C12.1804 17.0871 9.13257 16.7866 7.188 14.8415C6.10716 13.7604 5.49998 12.2942 5.5 10.7655Z"
									stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M17.029 16.5295L19.5 19.0005" stroke="#000000" stroke-width="1.5"
									stroke-linecap="round" stroke-linejoin="round" />
							</svg></button>
					</form>
					</div>
				</div>
			</div><!-- /header-bottom -->



