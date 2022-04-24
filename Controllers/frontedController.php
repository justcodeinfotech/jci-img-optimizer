<?php
if (!class_exists('jciwc_frontedController')) {
    class jciwc_frontedController
    {
        public function __construct()
        {
            /*
            * desperated in V1.0 
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


            // Loop for all the images available in dom 
            foreach ($images as $img) {
                // loop for particular IMG with src and diffrent SrcSets 

                if (!empty($img)) {
                    foreach ($img as $img_url) {
                        $content = str_replace($img_url, $this->wc_attechment_src($img_url), $content); // replace orignal image with webp images
                    }
                }
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
            $img_arr = [];
            $dom = new DOMDocument();
            $dom->loadHTML($image);
            $tags = $dom->getElementsByTagName('img');
            foreach ($tags as $tag) {
                $img_arr[] = $tag->getAttribute('src');
                $src_set_url = $tag->getAttribute('srcset');
                if (!empty($src_set_url)) {
                    $src_set_url = explode(",", $src_set_url);
                    foreach ($src_set_url as $url) {
                        $img_arr[] = strtok(trim($url), ' '); // get only url remove the size params 
                    }
                }
            }

            return array_unique($img_arr);
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
