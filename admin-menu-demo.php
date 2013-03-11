<?php  # -*- coding: utf-8 -*-
/**
 * Plugin Name: T5 Admin Menu Demo
 * Description: Load scripts and styles on specific admin menu pages
 */

add_action( 'admin_menu', array ( 'T5_Admin_Page_Demo', 'admin_menu' ) );

class T5_Admin_Page_Demo
{
	public static function admin_menu()
	{
		$main = add_menu_page(
			'T5 Demo',
			'T5 Demo',
			'manage_options',
			't5-demo',
			array ( __CLASS__, 'render_page' )
		);

		$sub = add_submenu_page(
			't5-demo',
			'T5 Demo Sub',
			'T5 Demo Sub',
			'manage_options',
			't5-demo-sub',
			array ( __CLASS__, 'render_page' )
		);

		foreach ( array ( $main, $sub ) as $slug )
		{
			add_action(
				"admin_print_styles-$slug",
				array ( __CLASS__, 'enqueue_style' )
			);
			add_action(
				"admin_print_scripts-$slug",
				array ( __CLASS__, 'enqueue_script' )
			);
		}

		//print "$main | $sub";
	}

	public static function render_page()
	{
		global $title;

		print '<div class="wrap">';
		print "<h1>$title</h1>";

		submit_button( 'Click me!' );

		print '<h2>Global variables</h2><table class="code">';

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

		print '</table></div>';
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
}