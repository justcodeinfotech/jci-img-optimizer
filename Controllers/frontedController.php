<?php
if (!class_exists('wc_frontedController')) {
    class wc_frontedController
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

                    // Fetching headers
                    $headers = @get_headers($comporessIMgurl);
                    // Use condition to check the existence of URL

                    if ($headers && strpos($headers[0], '200')) {
                        $image[0] = $comporessIMgurl;
                    }
                }
            }

            return $image;
        }
    }
    new wc_frontedController();
}
