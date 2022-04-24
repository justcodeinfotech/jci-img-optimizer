<?php
if (!class_exists('jciwc_ajaxController')) {
    class jciwc_ajaxController
    {
        public $ajaxList = [];
        public function __construct()
        {
            $this->ajaxList = [
                'wc_webp_optimize', // optimize images now
            ];

            /* load Wp ajax */
            $this->load_ajax();
        }

        private function load_ajax()
        {
            $ajaxlist = $this->ajaxList;
            if (!empty($ajaxlist)) {
                foreach ($ajaxlist as $ajax) {
                    add_action('wp_ajax_nopriv_' . $ajax, [$this, $ajax]);
                    add_action('wp_ajax_' . $ajax, [$this, $ajax]);
                }
            }
        }

        public function wc_webp_optimize()
        {
            $data = isset($_POST['data']) ? $_POST['data'] : '';
            parse_str($data, $data); // unserlize form data
            $data = jciwc_senitize_array($data); // sanitize whole array before processing the data

            if (empty($data) || !isset($data['wc_optimize_nonce'])) {
                $response = array('success' => true, 'error' => 'Bosted 1 !!');  //if $data is set
                wp_send_json($response);
                exit;
            }

            /* if anyone try to submit directlly */
            if (isset($data['wc_optimize_nonce']) && !wp_verify_nonce($data['wc_optimize_nonce'], 'wc_optimize_nonce')) {
                $response = array('success' => true, 'error' => 'Bosted 2 !!');  //if $data is set
                wp_send_json($response);
                exit;
            }

            // Get count of all attechments in library
            $query_optimized_images_args = array(
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'post_status'    => 'inherit',
                'posts_per_page' => 1,
                'fields' => 'ids',
                'no_found_rows' => true,
                'meta_query'    => array(
                    array(
                        'key' => 'jci_wc_optimized',
                        'compare' => 'NOT EXISTS'
                    ),
                )
            );

            $query_optimized_images = new WP_Query($query_optimized_images_args);
            $optimization_images = count($query_optimized_images->posts);

            $data = '';
            if ($optimization_images > 0) {

                $config = get_option('jci_wc_config', 1); // get settings config
                $newIMG_Quality = empty($config['img_quality']) ? 95 : $config['img_quality'];
                $newIMG_Resize = empty($config['img_resize']) ? 1800 : $config['img_resize'];

                foreach ($query_optimized_images->posts as $imageID) {
                    $already_update = 0;
                    $metadata = wp_get_attachment_metadata($imageID);


                    if (!empty($metadata['sizes'])) {
                        // Wordpress all images 
                        $sizearr = $metadata['sizes'];

                        $file_path = wp_get_original_image_path($imageID); //image path
                        $file_name = basename($file_path); // filename
                        $folder_path =  str_replace($file_name, "", $file_path); // file-Folder path

                        // First optimize orignal image
                        $orignalIMG = wp_get_original_image_url($imageID);

                        if (!empty($orignalIMG)) {
                            $img_file_name = basename($orignalIMG); // filename
                            $data = jciwc_convertImageToWebP($folder_path . $img_file_name, $folder_path, $newIMG_Quality, $newIMG_Resize);
                            if ($data == 1) {
                                $already_update = 1;
                                update_post_meta($imageID, 'jci_wc_optimized', 1);
                            }

                            /*  
                            * Based on origanal image URL we fetch the scaled image and also compress them
                            * Mostly this function us used for bigger resolution images
                            */
                            $ext = pathinfo($img_file_name, PATHINFO_EXTENSION); // get extension
                            $img_file_name =  str_replace('.' . $ext, '', $img_file_name); // file name without extension
                            $img_file_name = $img_file_name . '-scaled.' . $ext; // scaled img name 
                            $data = jciwc_convertImageToWebP($folder_path . $img_file_name, $folder_path, $newIMG_Quality, $newIMG_Resize);
                            if ($data == 1 && $already_update == 0) {
                                $already_update = 1;
                                update_post_meta($imageID, 'jci_wc_optimized', 1);
                            }
                        }


                        // Optimized all resize images
                        foreach ($sizearr as $key => $val) {
                            $img_atts = wp_get_attachment_image_src($imageID, $key);
                            if (!empty($img_atts[0])) {
                                $img_file_name = basename($img_atts[0]); // filename
                                $data = jciwc_convertImageToWebP($folder_path . $img_file_name, $folder_path, $newIMG_Quality, $newIMG_Resize);
                                if ($data == 1 && $already_update == 0) {
                                    $already_update = 1;
                                    update_post_meta($imageID, 'jci_wc_optimized', 1);
                                }
                            }
                        }
                    } else {

                        // Work when there is only 1 image (e.g : small img or not enabled gd extension )
                        $file_path = wp_get_original_image_path($imageID); //image path
                        $file_name = basename($file_path); // filename
                        $folder_path =  str_replace($file_name, "", $file_path); // file-Folder path
                        $img_atts = wp_get_original_image_url($imageID);

                        if (!empty($img_atts[0]) && is_array($img_atts)) {
                            $img_file_name = basename($img_atts[0]); // filename
                            $data = jciwc_convertImageToWebP($folder_path . $img_file_name, $folder_path, $newIMG_Quality, $newIMG_Resize);
                        } elseif (!empty($img_atts)) {
                            $img_file_name = basename($img_atts); // filename
                            $data = jciwc_convertImageToWebP($folder_path . $img_file_name, $folder_path, $newIMG_Quality, $newIMG_Resize);
                        }

                        if ($data == 1 && $already_update == 0) {
                            update_post_meta($imageID, 'jci_wc_optimized', 1);
                        }
                    }
                }
            }

            $response = array(
                'success' => true,
                'optimize_img' => $optimization_images
            );

            wp_send_json($response);
            exit;
        }
    }
    new jciwc_ajaxController();
}
