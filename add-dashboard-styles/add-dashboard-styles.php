<?php
/**
 *
 * Plugin Name: Add Dashboard Styles
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin add custom styles to the WordPress dashboard.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


# Add new styles

function dashboard_custom_style(){
    wp_enqueue_style( 'my-admin-theme', plugins_url( 'css/dashboard-styles.css', __FILE__ ) );
}

add_action( 'admin_enqueue_scripts', 'dashboard_custom_style' );
add_action( 'login_enqueue_scripts', 'dashboard_custom_style' );

?>
