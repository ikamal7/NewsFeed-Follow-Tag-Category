<?php
/*
 * Plugin Name: NewsFeed Follow Category
 * Plugin URI:https://pixelplugin.co
 * Description: Follow newsfeed for user's
 * Version: 1.0
 * Author: Kamal Hosen
 * Author URI: https://pixelplugin.co
 * Text Domain: nfc
 * Domain Path: /languages/
 * License: GNU General Public License v2 or later
 */

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';

class NewsfeedFolllow {

    public function __construct() {
        $this->define_constant();
        add_action( 'plugins_loaded', [$this, 'plugin_init'] );
        add_action( 'wp_enqueue_scripts', [$this, 'nfc_enqueue_script'] );
        add_action( 'widgets_init', [new \KAMAL\NFC\Admin\NFC_Widget, 'nfc_widget_reg'] );
        register_activation_hook( __FILE__, [$this, 'activate'] );

    }

    /**
     * @return mixed
     */
    public static function init() {
        /**
         * @var mixed
         */
        static $instance = false;
        if ( !$instance ) {
            $instance = new self();
        }
        return $instance;

    }

    /**
     * plugin_init
     *
     * @return void
     */
    public function plugin_init() {

    }

    /**
     * define_constant
     *
     * @return void
     */
    public function define_constant() {
        define( 'NFC_FILE', __FILE__ );
        define( 'NFC_PATH', __DIR__ );
        define( 'NFC_URL', plugins_url( '', NFC_FILE ) );
        define( 'NFC_ASSETS', NFC_URL . '/assets' );
    }

    /**
     * activate
     *
     * @return void
     */
    public function activate() {

    }

    /**
     * deactivate
     *
     * @return void
     */
    public function deactivate() {

    }

    public function nfc_enqueue_script() {

        wp_enqueue_script( 'nfc_script', NFC_ASSETS . '/js/nfc_scripts.js', ['jquery'], time(), true );
        wp_localize_script(
            'nfc_script',
            'nfc_data',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
            ]
        );
    }

}

/**
 * nfc_init
 *
 * @return void
 */
function nfc_init() {
    return NewsfeedFolllow::init();
}

nfc_init();
