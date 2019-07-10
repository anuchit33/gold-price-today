<?php
/*
Plugin Name: Gold Price Today
Plugin URI: https://github.com/anuchit33/gold-price-today
Description: ราคาทองคำวันนี้
Author: Anuchit Yai-in
Version: 0.0.1
Author URI: https://github.com/anuchit33/
*/

class GoldPriceToday {

    function __construct() {

        # Activation / Deactivation Hooks
        register_activation_hook(__FILE__, array($this, 'wp_activation'));
        register_deactivation_hook(__FILE__, array($this, 'wp_deactivation'));

        # Shortcode
        add_shortcode('gold-price-today', array($this, 'wp_shortcode_display'));

        # add_action admin_menu
        add_action('admin_menu', array($this, 'wp_add_menu'));

        # add action get
        add_action('wp_ajax_get_gold_price', array($this, 'wp_api_get_gold_price'));
        add_action('wp_ajax_nopriv_get_gold_price', array($this, 'wp_api_get_gold_price'));
    }

    function wp_activation(){

    }

    function wp_deactivation(){

    }

    function wp_shortcode_display(){

            ob_start();
            require_once( dirname(__FILE__) . '/templates/frontend/table-gold-price.php');
            $content = ob_get_contents();
            ob_end_clean();
    
            return $content;
        
    }

    function wp_add_menu(){


        $page_title = 'Gold Price Today';
        $menu_title = 'ราคาทองวันนี้';
        $capability = 'read'; // manage_options , read
        $menu_slug = 'gold-price-today';
        $function = '';
        $icon_url = 'dashicons-chart-bar';
        $position = '2.2.10';

        add_menu_page($page_title , $menu_title, $capability, $menu_slug ,$function , $icon_url, $position);

        // add sub menu 1
        $sub_parent_slug = $menu_slug;
        $sub_page_title =  $menu_title.'ราคาทองวันนี้';
        $sub_menu_title = 'ราคาทองวันนี้';
        $sub_menu_slug = 'gold-price-today';
        $sub_capability = 'read';
        
        add_submenu_page($sub_parent_slug, $sub_page_title, $sub_menu_title , $sub_capability, $sub_menu_slug , array(__CLASS__, 'wp_page_admin'));
        
    }

    function wp_page_admin(){

        include( dirname(__FILE__) . '/templates/admin/page-shortcode.php' );
    }


    function wp_api_get_gold_price(){

        # check ajax_security
        check_ajax_referer('ajax_security', 'security');

        # filter date
        $date = isset($_GET['date'])?$_GET['date']:date('Y-m-d');

        # fetch gold price
        $args = array();
        $url = 'https://www.aagold-th.com/price/daily/?date='.$date;
        $response = wp_remote_get( $url );
        $body = wp_remote_retrieve_body( $response );
        wp_send_json(json_decode($body,true)[0],200);
        die();
    }

}

new GoldPriceToday();