<?php

/**
 * Plugin Name: Webp Converter and compressor 
 * Description: This plugin is used to compress your media library to web image to optimize page speed and display compressed version in fronted
 * Version: 1.0
 * Author: Justcode Infotech
 * Author URI: https://justcodeinfotech.com/
 * Requires at least: 4.4
 * Tested up to: 5.9
 * Text Domain: jci-webp-compressor
 * Stable tag: 1.0
 * Requires PHP: 5.4
 **/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

## define
if (!defined('JCI_WC_VERSION')) {
    define('JCI_WC_VERSION', '1.0.0'); // current plugin version
}
if (!defined('JCI_WC_FILE')) {
    define('JCI_WC_FILE', __FILE__);
}
if (!defined('JCI_WC_URL')) {
    define('JCI_WC_URL', plugin_dir_url(JCI_WC_FILE));
}
if (!defined('JCI_WC_PATH')) {
    define('JCI_WC_PATH', plugin_dir_path(JCI_WC_FILE));
}

## load inc to initialize plugin
require JCI_WC_PATH . '/inc/inc.php';
