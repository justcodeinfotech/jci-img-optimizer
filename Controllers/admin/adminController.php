<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('JCI_WC_adminController')) {
    class JCI_WC_adminController
    {
        public function __construct()
        {

            if (!empty($_POST) && isset($_POST['form_base'])) {

                $this->wc_has_access(); // if user have Access to view page then save it 

                /* Save the form data */
                $this->save_form($_POST);
            }

            /* Load admin menu */
            add_action('admin_menu', array($this, 'jci_wc_admin_menu'), 100);

            /* Load css and JS */
            add_action('admin_enqueue_scripts', [$this, 'wc_admin_css_js']);

            add_action('delete_attachment', [$this, 'jci_wc_delete_attachment']);
        }

        public function jci_wc_admin_menu()
        {
            add_menu_page('jci-webp-compressor', 'Webp Converter', 'manage_options', 'jci-webp-compressor', array($this, 'wc_load_page'), 'dashicons-format-image', 25);
        }

        public function wc_load_page()
        {
            $this->wc_has_access(); // if user have Access

            $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '';

            if ($tab != '' && $tab == 'compress') {
                include_once JCI_WC_PATH . 'views/admin/compress.php'; // get compress setiings with chart 
            } else {
                $config = get_option('jci_wc_config', 1); // get settings config
                if (!empty($config) && is_array($config)) {
                    extract($config);
                }
                include_once JCI_WC_PATH . 'views/admin/general-settings.php'; // get setiings view 
            }
        }

        public function wc_has_access()
        {
            // If user do not have access permission
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
        }


        public function wc_admin_css_js()
        {
            $pagenow = sanitize_text_field(isset($_GET['page']) ? $_GET['page'] : '');
            $wc_page_list = ['jci-webp-compressor']; //page list used in wc-firebase plugin

            /* If its not our plugin page  */
            if (!in_array($pagenow, $wc_page_list)) {
                return;
            }

            if (!wp_script_is('jquery', 'enqueued')) {
                wp_enqueue_script('jquery');
            }

            wp_enqueue_style('wc-admin', JCI_WC_URL . 'assets/admin/css/admin.min.css', __FILE__);
            wp_enqueue_script('wc-chart', JCI_WC_URL . 'assets/admin/js/chart.min.js', array(), JCI_WC_VERSION, true);
            // wp_enqueue_script('wc-admin', JCI_WC_URL . 'assets/admin/js/admin.min.js', array(), JCI_WC_VERSION, true);
            wp_enqueue_script('wc-admin', JCI_WC_URL . 'assets/admin/js/admin.min.js', array(), time(), true);
            wp_localize_script('wc-admin', 'wc_obj', array('ajax_url' => admin_url('admin-ajax.php')));
        }

        public function save_form($data)
        {
            if (isset($data['form_base']) && $data['form_base'] == 'img_config') {

                unset($data['form_base']);
                $sanitized_arr = jciwc_senitize_array($data); // sanitize whole array before add to database

                /* if verify nonce then save the data only */
                if (isset($sanitized_arr['wc_general_settings_nonce']) && wp_verify_nonce($sanitized_arr['wc_general_settings_nonce'], 'wc_general_settings_nonce')) {
                    unset($sanitized_arr['wc_general_settings_nonce']); // unset the nonce 
                    unset($sanitized_arr['_wp_http_referer']); // unset refer
                    update_option('jci_wc_config', $sanitized_arr);
                }
            }
        }

        public function jci_wc_delete_attachment($attachment_id)
        {
            require_once 'delete_attachment.php';
            $delete_attachment =  new jci_wc_delete_attachment();
            $delete_attachment->delete_attachment($attachment_id); // delete the attachment
        }
    }
    new JCI_WC_adminController();
}
