<?php  # -*- coding: utf-8 -*-
/**
 * Plugin Name: T5 Admin Menu Demo
 * Description: Load scripts and styles on specific admin menu pages
 * Plugin URI:  https://github.com/toscho/T5-Admin-Menu-Demo
 * Version:     2013.03.12
 * Author:      Thomas Scholz
 * Author URI:  http://toscho.de
 * Licence:     MIT
 * License URI: http://opensource.org/licenses/MIT
 */

add_action( 'admin_menu', array ( 'T5_Admin_Page_Demo', 'admin_menu' ) );

/**
 * Register two admin pages and add a stylesheet and a javascript to both only.
 * @author toscho
 *
 */
class T5_Admin_Page_Demo
{
	/**
	 * Register the pages and the style and script loader callbacks.
	 *
	 * @wp-hook admin_menu
	 * @return  void
	 */
	public static function admin_menu()
	{
		// $main is now a slug named "toplevel_page_t5-demo"
		// built with get_plugin_page_hookname( $menu_slug, '' )
		$main = add_menu_page(
			'T5 Demo',                         // page title
			'T5 Demo',                         // menu title
			'manage_options',                  // capability
			't5-demo',                         // menu slug
			array ( __CLASS__, 'render_page' ) // callback function
		);

		// $sub is now a slug named "t5-demo_page_t5-demo-sub"
		// built with get_plugin_page_hookname( $menu_slug, $parent_slug)
		$sub = add_submenu_page(
			't5-demo',                         // parent slug
			'T5 Demo Sub',                     // page title
			'T5 Demo Sub',                     // menu title
			'manage_options',                  // capability
			't5-demo-sub',                     // menu slug
			array ( __CLASS__, 'render_page' ) // callback function, same as above
		);

		foreach ( array ( $main, $sub ) as $slug )
		{
			// make sure the style callback is used on our page only
			add_action(
				"admin_print_styles-$slug",
				array ( __CLASS__, 'enqueue_style' )
			);
			// make sure the script callback is used on our page only
			add_action(
				"admin_print_scripts-$slug",
				array ( __CLASS__, 'enqueue_script' )
			);
		}

		// $sub is now a slug named "t5-demo_page_t5-demo-sub"
		// built with get_plugin_page_hookname( $menu_slug, $parent_slug)
		$text = add_submenu_page(
			't5-demo',                         // parent slug
			'Text Included',                     // page title
			'Text Included',                     // menu title
			'manage_options',                  // capability
			't5-text-included',                     // menu slug
			array ( __CLASS__, 'render_text_included' ) // callback function, same as above
		);
	}

	/**
	 * Print page output.
	 *
	 * @wp-hook toplevel_page_t5-demo In wp-admin/admin.php do_action($page_hook).
	 * @wp-hook t5-demo_page_t5-demo-sub
	 * @return  void
	 */
	public static function render_page()
	{
		global $title;

		print '<div class="wrap">';
		print "<h1>$title</h1>";

		submit_button( 'Click me!' );

		$backtrace = debug_backtrace();
		self::list_backtrace( $backtrace );
		self::list_globals();
		print '</div>';
	}

	public static function render_text_included()
	{
		global $title;

		print '<div class="wrap">';
		print "<h1>$title</h1>";

		$file = plugin_dir_path( __FILE__ ) . "included.html";

		if ( file_exists( $file ) )
			require $file;

		print "<p class='description'>Included from <code>$file</code></p>";

		print '</div>';

	}

	public static function enqueue_style()
	{
		wp_register_style(
			't5_demo_css',
			plugins_url( 't5-demo.css', __FILE__ )
		);
		wp_enqueue_style( 't5_demo_css' );
	}

	public static function enqueue_script()
	{
		wp_register_script(
			't5_demo_js',
			plugins_url( 't5-demo.js', __FILE__ ),
			array(),
			FALSE,
			TRUE
		);
		wp_enqueue_script( 't5_demo_js' );
	}

	protected static function list_globals()
	{
		print '<h2>Global variables</h2><table class="code">';

		ksort( $GLOBALS );
		foreach ( $GLOBALS as $key => $value )
		{
			print '<tr><td>$' . esc_html( $key ) . '</td><td>';

			if ( ! is_scalar( $value ) )
			{
				print '<var>' . gettype( $value ) . '</var>';
			}
			else
			{
				if ( FALSE === $value )
					$show = '<var>FALSE</var>';
				elseif ( '' === $value )
				$show = '<var>""</var>';
				else
					$show = esc_html( $value );

				print $show;
			}

			print '</td></tr>';
		}

		print '</table>';
	}

	protected static function list_backtrace( $backtrace )
	{
		print '<h2>debug_backtrace()</h2><ol class="code">';

		foreach ( $backtrace as $item )
		{
			print '<li>';
			if ( isset ( $item['class'] ) )
				print $item['class'] . $item['type'];

			print $item['function'];

			if ( isset ( $item['args'] ) )
				print '<pre>args = ' . print_r( $item['args'], TRUE ) . '</pre>';

			if ( isset ( $item['file'] ) )
				print '<br>' . $item['file'] . ' line: ' . $item['line'];

			print "\n";
		}

		print '</ol>';
		// . htmlspecialchars( print_r( $back_trace, TRUE ), ENT_QUOTES, 'utf-8', FALSE ) . '</pre>';

	}
}