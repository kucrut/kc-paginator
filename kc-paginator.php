<?php

/**
 * @package KC_Paginator
 * @version 0.1
 */


/*
Plugin name: KC Paginator
Plugin URI: http://kucrut.org/kc-paginator/
Description: A continuation of the <a href="http://wordpress.org/extend/plugins/paginator/">Paginator</a> plugin originally developed by Dhuz.
Version: 0.1
Author: Dzikri Aziz
Author URI: http://kucrut.org/
License: GPL v2
Text Domain: kc-settings
*/

final class kcPaginator {
	protected static $doscript = false;


	public static function init() {
		$url = plugins_url( '/kc-paginator/sns' );
		wp_register_script( 'jquery-jscrollpane', "{$url}/jquery.jscrollpane.min.js", array('jquery'), '2.0.0beta12', true );
		wp_enqueue_style( 'kc-paginator', apply_filters( 'kc_paginator_css', "{$url}/style.css" ), false, '0.1' );

		add_action( 'wp_footer', array(__CLASS__, 'script'), 100 );
	}


	public static function paginator( $query = false ) {
		if ( !$query ) {
			global $wp_query;
			$query = $wp_query;
		}

		if ( !is_object($query) )
			return;

		$current = max( 1, $query->query_vars['paged'] );
		$big = 999999999;

		$pagination = array(
			'base'    => str_replace( $big, '%#%', get_pagenum_link($big) ),
			'format'  => '',
			'total'   => $query->max_num_pages,
			'current' => $current,
			'type'    => 'array',
			'show_all' => true,
			'prev_next' => false
		);
		$links = paginate_links($pagination);
		if ( empty($links) )
			return; ?>
	<nav class="kc-paginator">
		<div class="jsp">
			<ul>
				<?php foreach ( $links as $item ) { ?>
				<li><?php echo $item ?></li>
				<?php } ?>
			</ul>
		</div>
		<span class="pnum"><?php printf( __('Page %1$s of %2$s pages', 'kc-paginator'), '<span class="current">'.$current.'</span>', '<span class="total">'.count($links).'</span>' ) ?></span>
	</nav>
	<?php
		self::$doscript = true;
		wp_enqueue_script( 'jquery-jscrollpane' );
	}


	public static function script() {
		if ( !self::$doscript )
			return;
	?>
<script>
jQuery(document).ready(function($) {
	var $kcPg = $('nav.kc-paginator > div.jsp').jScrollPane({
		horizontalDragMinWidth: 23,
		horizontalDragMaxWidth: 23,
		enableKeyboardNavigation: false,
		hideFocus: true,
		horizontalGutter: 0
	});

	$(window).resize(function() {
		$kcPg.data('jsp').reinitialise();
	});
});
</script>
	<?php }
}
add_action( 'wp_enqueue_scripts', array('kcPaginator', 'init'), 7 );


function kc_paginator( $query = false ) {
	kcPaginator::paginator( $query );
}
