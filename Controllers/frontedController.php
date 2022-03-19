<?php
if (!class_exists('jciwc_frontedController')) {
    class jciwc_frontedController
    {
        public function __construct()
        {
            add_filter('wp_get_attachment_image_src', [$this, 'wc_attechment_src'], 10, 4);
        }

        public function wc_attechment_src($image, $attachment_id, $size, $icon)
        {
            $imgURL = isset($image[0]) ? $image[0] : '';
            if (!empty($imgURL)) {
                $ext = pathinfo($imgURL, PATHINFO_EXTENSION);

                $supported_arr = ['jpeg', 'jpg', 'gif', 'png'];
                if (in_array($ext, $supported_arr)) {
                    $info = pathinfo($imgURL);
                    $comporessIMgurl = $info['dirname'] . '/' . $info['filename'] . '.webp';

                    $compressedimgPath = $_SERVER['DOCUMENT_ROOT'] . '/' . wp_make_link_relative($comporessIMgurl);

                    if (file_exists($compressedimgPath)) {
                        // if compress image file exsist then display 
                        $image[0] = $comporessIMgurl;
                    }
                }
            }

            return $image;
        }
    }
    new jciwc_frontedController();
}
