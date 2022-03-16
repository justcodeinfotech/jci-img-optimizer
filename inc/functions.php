<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!function_exists('jciwc_senitize_array')) {
    // senitized the whole array from post
    function jciwc_senitize_array($array)
    {
        if (!empty($array) && (is_array($array) || is_object($array))) {
            foreach ($array as $key  => $val) {

                if (is_array($val) || is_object($val)) {
                    $senitized_array[$key] = jciwc_senitize_array($val); // if inside array then call function again
                } else {
                    // senitized key and value
                    $senitized_array[sanitize_key($key)] = sanitize_text_field($val); // if not array then directly push it 
                }
            }
        } else {
            $senitized_array[] = sanitize_text_field($array);
        }
        return $senitized_array;
    }
}

if (!function_exists('jciwc_convertImageToWebP')) {
    function jciwc_convertImageToWebP($source, $destination, $quality = 80, $resize = 3000)
    {
        ini_set('memory_limit', '1G');
        set_time_limit(120);

        $newimg = '';
        if (!file_exists($source)) {
            return 1; // if file is not exsit
        }

        $newName = basename($source);
        $newName = preg_replace("/\.[^.]+$/", "", $newName);
        $newName = $newName . '.webp';

        $newimg_path = $destination . $newName;
        $image_extension = pathinfo($source, PATHINFO_EXTENSION);
        $methods = array(
            'jpg' => 'imagecreatefromjpeg',
            'jpeg' => 'imagecreatefromjpeg',
            'png' => 'imagecreatefrompng',
            'gif' => 'imagecreatefromgif'
        );

        if (!isset($methods[$image_extension])) {
            return 1; // if file is not exsit
        }

        $image = @$methods[$image_extension]($source);
        list($width, $height) = getimagesize($source);

        if ($width > $resize) {

            $new_width = $resize;
            $new_height = round($height / $width * $resize);

            $dst = imagecreatetruecolor($new_width, $new_height);
            imagecopyresized($dst, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            $image = $dst;
        }

        imageistruecolor($image);
        imagepalettetotruecolor($image);
        if (!file_exists($newimg_path)) {
            $newimg = imagewebp($image, $newimg_path, $quality);
        } else {
            $newimg = 1;
        }

        return $newimg;
    }
}
