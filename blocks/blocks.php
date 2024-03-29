<?php

/**
 * Register Blocks
 * @see https://www.billerickson.net/building-gutenberg-block-acf/#register-block
 **/

function dentaly_block_categories($categories, $post)
{
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'forms',
				'title' => 'Forms',
			),
			array(
				'slug' => 'Style & Design',
				'title' => 'style',
			),
		)
	);
}

add_filter('block_categories', 'dentaly_block_categories', 10, 2);

function dentaly_block_template_callback($block)
{
	// convert name ("acf/testimonial") into path friendly slug ("testimonial")
	$slug = str_replace('acf/', '', $block['name']);


	if (file_exists(get_stylesheet_directory() . "/blocks/{$slug}/{$slug}.php")) {
		include(get_stylesheet_directory() . "/blocks/{$slug}/{$slug}.php");
	}
}

add_action('acf/init', function () {
	remove_filter('acf_the_content', 'wpautop');
}, 15);

function block_assets_callback($block)
{
	// convert name ("acf/testimonial") into path friendly slug ("testimonial")
	$blockName = str_replace('acf/', '', $block['name']);

	// check if blocks aren't specified on page(reusable block) or they appear in admin
	// and add styles after ACF block initialized(default ACF block way)
	if (!has_block('acf/' . $blockName) || is_admin()) {
		wp_register_style($blockName, '' . get_stylesheet_directory_uri() . '/assets/css/blocks/' . $blockName . '.css');
		wp_enqueue_style($blockName);
	}

	if (!is_admin()) {
		switch ($blockName) {
				// case 'accordion-block':
				// 	wp_enqueue_script('accordion-block', '' . get_stylesheet_directory_uri() . '/blocks/' . $blockName . '/' . $blockName . 	'.js', null, false);
				// 	break;
		}
	}
}


/**
 * Register the styles (CSS) for the blocks outside
 * acf_register_block_type() as loading styles
 * using acf_register_block_type() will load the
 * styles in the footer and not in <head> causing
 * CLS issues
 */
function register_acf_block_styles()
{
	foreach (acf_get_block_types() as $acfBlock) {
		$blockName = str_replace('acf/', '', $acfBlock['name']);

		if (has_block('acf/' . $blockName)) {
			wp_register_style($blockName, '' . get_stylesheet_directory_uri()  . '/assets/css/blocks/' . $blockName . '.css', null);
			wp_enqueue_style($blockName);
		}
	}
}

add_action('wp_head', 'register_acf_block_styles', 1);
add_action('admin_enqueue_scripts', 'register_acf_block_styles', 1);


function dentaly_register_blocks()
{

	if (!function_exists('acf_register_block')) return;

	acf_register_block(array(
		'name' => 'cars-block',
		'title' => 'Cars Block',
		'enqueue_assets' => 'block_assets_callback',
		'render_callback' => 'dentaly_block_template_callback',
		'category' => 'layout',
		'icon' => 'editor-quote',
		'keywords' => array(
			'cars', 'car',
			'block '
		)
	));
}


add_action('acf/init', 'dentaly_register_blocks');
