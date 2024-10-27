<?php

/*
Plugin Name: AdsInserter
Description: Site ads and widgets placement manager
Version: 1.7
Author: e.nechehin
Author URI: https://adsinserter.com
*/
/*  Copyright 2018-2023  e.nechehin  (email: nechehin@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}


require WP_PLUGIN_DIR . '/adsinserter/options.php';


/**
 * Add DNS prefetch and page global tags
 */
add_action('wp_head', function(){

	$tags = array_values(get_body_class());

	// Post category
	if (is_single()) {
		global $post;
		foreach((get_the_category($post)) as $category) {
			$tags[] = 'category-' . $category->category_nicename;
		}
	}

	echo '<link rel="dns-prefetch" href="//aixcdn.com" />' . PHP_EOL;
	echo '<script type="text/javascript">' . PHP_EOL;
	echo 'var adsinserter = adsinserter || {};' . PHP_EOL;
	echo 'adsinserter.tags = ' . json_encode(array_unique($tags)) . ';' . PHP_EOL;
	echo '</script>' . PHP_EOL;
}, 0);

/**
 * Add client script
 */
add_action('wp_footer', function(){

	$adsInserterEnabled = apply_filters('adsinserter-enabled', true);

	if (!$adsInserterEnabled) {
		return;
	}

	echo '<script type="text/javascript">
    (function(a, i){
        var s = a.createElement(\'script\');
        s.src = i + \'?\' + Math.ceil(Date.now()/10000000);
        a.getElementsByTagName(\'head\')[0].appendChild(s);
    })(document, \'https://aixcdn.com/client.js\');
</script>' . PHP_EOL;
});

/**
 * Register widgets
 */
add_action('widgets_init', function() {
	require WP_PLUGIN_DIR . '/adsinserter/widgets/placement.php';
	register_widget( 'AdsInserter_Placement_Widget' );
});


/**
 * Add options page in admin dashboard
 */
if ( is_admin() ) {
	$aiOptionsPage = new AdsInserter_Options();
}


/**
 * In text placement
 */
add_filter('the_content', function($content){

	if (is_feed()) {
		return $content;
	}

	if (!is_single()) {
		return $content;
	}

	if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
		return $content;
	}

	$options = get_option(AdsInserter_Options::OPTIONS);

	if (!$options) {
		return $content;
	}

	$options = array_merge([
		AdsInserter_Options::OPTION_INTEXT_ENABLED => false,
		AdsInserter_Options::OPTION_INTEXT_PLACEMENT_ID => 0,
		AdsInserter_Options::OPTION_INTEXT_START => 1,
		AdsInserter_Options::OPTION_INTEXT_DENSITY => 3,
	], $options);

	if (!$options[AdsInserter_Options::OPTION_INTEXT_ENABLED]) {
		return $content;
	}

	if (!$options[AdsInserter_Options::OPTION_INTEXT_PLACEMENT_ID]) {
		return $content;
	}

	if (!$options[AdsInserter_Options::OPTION_INTEXT_DENSITY]) {
		return $content;
	}

	$p_array = explode('</p>', $content);
	$p_count = count($p_array);
	$out_content = '';
	$counter = 0;

	$aiPlacementCode = sprintf(
		'<div class="ai-placement" data-id="%d"></div>',
		$options[AdsInserter_Options::OPTION_INTEXT_PLACEMENT_ID]
	);

	for ($i = 0; $i < $p_count; $i++) {

		$out_content .= $p_array[ $i ] . '</p>';

		if (function_exists('mb_strlen')) {
			if (!mb_strlen(trim($p_array[ $i ]))) {
				continue;
			}
		} else {
			if (!strlen(trim($p_array[ $i ]))) {
				continue;
			}
		}

		if ($i >= $options[ AdsInserter_Options::OPTION_INTEXT_START ]) {

			if (! ( $counter % $options[ AdsInserter_Options::OPTION_INTEXT_DENSITY ] )) {
				$out_content .= $aiPlacementCode;
			}

			$counter++;
		}
	}

	return $out_content;
}, 20);



