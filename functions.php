<?php

// 'post-formats', 'post-thumbnails', 'custom-header', 'custom-background', 'custom-logo', 'menus', 'automatic-feed-links', 'html5', 'title-tag', 'customize-selective-refresh-widgets', 'starter-content', 'responsive-embeds', 'align-wide', 'dark-editor-style', 'disable-custom-colors', 'disable-custom-font-sizes', 'editor-color-palette', 'editor-font-sizes', 'editor-styles', 'wp-block-styles', and 'core-block-patterns'.

if ( ! function_exists( 'techiepress_theme_setup' ) ) {
	function techiepress_theme_setup() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		$args = array(
			'flex-width'    => true,
			'width'         => 1200,
			'flex-height'   => true,
			'height'        => 300,
			'default-image' => get_template_directory_uri() . '/assets/images/header.jpg',
		);
		add_theme_support( 'custom-header', $args );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'custom-logo', array(
			'height'      => 150,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'site-title', 'site-description' ),
		) );
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	}
}

add_action( 'after_setup_theme', 'techiepress_theme_setup' );


add_action( 'wp_enqueue_scripts', 'techiepress_wp_enqueue_scripts' );

function techiepress_wp_enqueue_scripts() {	
	
	wp_enqueue_style( 'techiepress-food', get_stylesheet_directory_uri() . '/assets/css/style.css', '', '1.0.0', 'all' );
	
	if ( is_front_page() ) {
		// Bootstrap & Fontawesome css for our tab navigation.
		wp_enqueue_style( 'bootstrap-css', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css', '', '1.0.0', 'all' );
		wp_enqueue_style( 'fontawesome-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css', '', '1.0.0', 'all' );

		// Bring in styles from bootstrap for our tab navigation.
		wp_enqueue_script( 'bootstrap-bundle', get_stylesheet_directory_uri() . '/assets/js/bootstrap.bundle.min.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'techiepress-food-add', get_stylesheet_directory_uri() . '/assets/js/food-add.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'techiepress-food-add', 'ajax_object',
		array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
	}

}

add_action('wp_ajax_food_ajax_add_to_cart', 'food_ajax_add_to_cart');
add_action('wp_ajax_nopriv_food_ajax_add_to_cart', 'food_ajax_add_to_cart');
        
function food_ajax_add_to_cart() {

            $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
            $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
            $values = $_POST['variation'];
			error_log(print_r($values, true));
			foreach ($values as $variation_id) {
				
				$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
				$product_status = get_post_status($product_id);
				$added = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, wc_get_product_variation_attributes( $variation_id ) );
				if ($passed_validation && isset($added) && 'publish' == $product_status) {

					do_action('woocommerce_ajax_added_to_cart', $product_id);

					if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
						wc_add_to_cart_message(array($product_id => $quantity), true);
					}
				// WC_AJAX :: get_refreshed_fragments();
				} else {

					$data = array(
						'error' => true,
						'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));

					echo wp_send_json($data);
				}
			}
            wp_die();
        }
