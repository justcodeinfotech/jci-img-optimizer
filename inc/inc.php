<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
register_activation_hook(JCI_WC_FILE, 'jci_ec_activate');

/* Load the necessary functions  */
require_once 'functions.php';

/* load ajax controller */
require_once JCI_WC_PATH . 'Controllers/ajaxController.php';

/* Load fronted Controller  */
require_once JCI_WC_PATH . 'Controllers/frontedController.php';

add_action('init', function () {
    if (is_admin()) {
        require_once JCI_WC_PATH . 'Controllers/admin/adminController.php';
    }
});


/**
 * Activation_hook.
 */
function jci_ec_activate()
{
    $config = get_option('jci_wc_config', 1); // get settings config
    // first time plugin active setup the default value
    if (empty($config['img_quality'])) {
        $config_arr = [
            'img_quality' => 95,
            'img_resize' => 1800,
        ];
        update_option('jci_wc_config', $config_arr);
    }
}
