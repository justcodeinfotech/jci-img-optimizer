<?php
if (!class_exists('jciwc_ajaxController')) {
    class jciwc_ajaxController
    {
        public $ajaxList = [];
        public function __construct()
        {
            $this->ajaxList = [
                'wc_firebase_config', // save admin webp config
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

                $config = get_option('jci_img_comfig', 1); // get settings config
                $newIMG_Quality = empty($config['img_quality']) ? 80 : $config['img_quality'];
                $newIMG_Resize = empty($config['img_resize']) ? 80 : $config['img_resize'];

                foreach ($query_optimized_images->posts as $imageID) {
                    $already_update = 0;
                    $metadata = wp_get_attachment_metadata($imageID);


                    if (!empty($metadata['sizes'])) {
                        // Wordpress all images 
                        $sizearr = $metadata['sizes'];

                        $file_path = wp_get_original_image_path($imageID); //image path
                        $file_name = basename($file_path); // filename
                        $folder_path =  str_replace($file_name, "", $file_path); // file-Folder path

                        // First optimize opriganl image
                        $orignalIMG = wp_get_original_image_url($imageID);

                        if (!empty($orignalIMG)) {
                            $img_file_name = basename($orignalIMG); // filename
                            $data = jciwc_convertImageToWebP($folder_path . $img_file_name, $folder_path, $newIMG_Quality, $newIMG_Resize);
                            if ($data == 1) {
                                $already_update = 1;
                                update_post_meta($imageID, 'jci_wc_optimized', 1);
                                $this->wc_webp_setimage($orignalIMG);
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
                                if ($data == 1) {
                                    $this->wc_webp_setimage($img_atts[0]);
                                }
                            }
                        }
                    } else {

                        // Work when there is only 1 image and it uploaded before using enable gd extention
                        $file_path = wp_get_original_image_path($imageID); //image path
                        $file_name = basename($file_path); // filename
                        $folder_path =  str_replace($file_name, "", $file_path); // file-Folder path
                        $img_atts = wp_get_original_image_url($imageID);

                        if (!empty($img_atts[0]) && is_array($img_atts)) {
                            $img_file_name = basename($img_atts[0]); // filename
                            $data = jciwc_convertImageToWebP($folder_path . $img_file_name, $folder_path, $newIMG_Quality, $newIMG_Resize);

                            if ($data == 1) {
                                $this->wc_webp_setimage($img_atts[0]);
                            }
                        } elseif (!empty($img_atts)) {
                            $img_file_name = basename($img_atts); // filename
                            $data = jciwc_convertImageToWebP($folder_path . $img_file_name, $folder_path, $newIMG_Quality, $newIMG_Resize);
                            if ($data == 1) {
                                $this->wc_webp_setimage($img_atts);
                            }
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


        private function wc_webp_setimage($imageURL)
        {
            $db_imageURL = $this->remove_http($imageURL);

            global $wpdb;
            $post_tbl = $wpdb->prefix . "posts";
            $sql = $wpdb->prepare("SELECT id,post_content  FROM $post_tbl WHERE `post_content` LIKE '%$db_imageURL%'");
            $post_with_old_img_url = $wpdb->get_results($sql); // the post list include the ues of all old images


            if (!empty($post_with_old_img_url)) {

                foreach ($post_with_old_img_url as $post) {
                    $postID = @$post->id;
                    $post_content = @$post->post_content;

                    if (empty($post_content))
                        continue;


                    $extension = pathinfo($db_imageURL, PATHINFO_EXTENSION);
                    $new_img_url =   str_replace($extension, "webp", $db_imageURL);

                    $compressedimgPath = $_SERVER['DOCUMENT_ROOT'] . wp_make_link_relative($imageURL);
                    if (file_exists($compressedimgPath)) {
                        // if compress image file exsist then display 
                        $new_post_content =  str_replace($db_imageURL, $new_img_url, $post_content);

                        $my_post = array(
                            'ID'           => $postID,
                            'post_content' => $new_post_content,
                        );

                        // Update the post into the database
                        wp_update_post($my_post);
                    }
                }
            }
        }

        private function remove_http($url)
        {
            $disallowed = array('http://', 'https://');
            foreach ($disallowed as $d) {
                if (strpos($url, $d) === 0) {
                    return str_replace($d, '', $url);
                }
            }
            return $url;
        }
    }
    new jciwc_ajaxController();
}
