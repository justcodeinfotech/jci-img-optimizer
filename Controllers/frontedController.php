<?php
if (!class_exists('jciwc_frontedController')) {
    class jciwc_frontedController
    {
        public function __construct()
        {
            /*
            * desperate V1.0 
            * add_filter('wp_get_attachment_image_src', [$this, 'wc_attechment_src'], 10, 4);
            */

            // Added in V-1.1
            add_action('template_redirect', [$this, 'start_content_process'], -1000);
        }

        public function start_content_process()
        {
            ob_start([$this, 'maybe_process_buffer']); // start the buffer 
        }

        public function maybe_process_buffer($buffer)
        {
            if (strlen($buffer) <= 255) {
                // Buffer length must be > 255 (IE does not read pages under 255 c).
                return $buffer;
            }

            $buffer = $this->process_content($buffer);

            return $buffer;
        }

        public function process_content($content)
        {
            $images = $this->get_images($content);

            if (!$images) {
                return $content; // If there is not any images
            }

            foreach ($images as $tag) {
                $content = str_replace($tag[0], $this->wc_attechment_src($tag[0]), $content); // replace orignal image with webp images
            }


            return $content;
        }

        protected function get_images($content)
        {
            // Remove comments.
            $content = preg_replace('/<!--(.*)-->/Uis', '', $content);

            if (!preg_match_all('/<img\s.*>/isU', $content, $matches)) {
                return [];
            }

            $images = array_map([$this, 'process_image'], $matches[0]);
            $images = array_filter($images);
            return $images;
        }

        protected function process_image($image)
        {
            preg_match_all('/<img(.*)src(.*)=(.*)"(.*)"/U', $image, $matches); // get src from img tag
            return array_pop($matches);
        }


        public function wc_attechment_src($imgURL)
        {
            if (!empty($imgURL)) {
                $ext = pathinfo($imgURL, PATHINFO_EXTENSION);
                $supported_arr = ['jpeg', 'jpg', 'gif', 'png'];
                if (in_array($ext, $supported_arr)) {
                    $info = pathinfo($imgURL);
                    $comporessIMgurl = $info['dirname'] . '/' . $info['filename'] . '.webp';

                    $compressedimgPath = $_SERVER['DOCUMENT_ROOT'] . '/' . wp_make_link_relative($comporessIMgurl);

                    if (file_exists($compressedimgPath)) {
                        // if compress image file exsist then display 
                        $imgURL = $comporessIMgurl;
                    }
                }
            }

            return $imgURL;
        }
    }
    new jciwc_frontedController();
}
