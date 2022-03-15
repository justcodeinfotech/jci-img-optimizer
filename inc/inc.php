<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
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
