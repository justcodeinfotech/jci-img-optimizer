<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!function_exists('wc_senitize_array')) {
    // senitized the whole array from post
    function wc_senitize_array($array)
    {
        if (!empty($array) && (is_array($array) || is_object($array))) {
            foreach ($array as $key  => $val) {

                if (is_array($val) || is_object($val)) {
                    $senitized_array[$key] = wc_senitize_array($val); // if inside array then call function again
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

if (!function_exists('wc_convertImageToWebP')) {
    function wc_convertImageToWebP($source, $destination, $quality = 80, $resize = 3000)
    {
        if (!file_exists($source)) {
            return; // if file is not exsit
        }

        $extension = pathinfo($source, PATHINFO_EXTENSION);
        if ($extension == 'jpeg' || $extension == 'jpg')
            $image = imagecreatefromjpeg($source);
        elseif ($extension == 'gif')
            $image = imagecreatefromgif($source);
        elseif ($extension == 'png')
            $image = imagecreatefrompng($source);

        $newName = basename($source);
        $newName = preg_replace("/\.[^.]+$/", "", $newName);
        $newName = $newName . '.webp';


        list($width, $height) = getimagesize($source);

        if ($width > $resize) {

            $new_width = $resize;
            $new_height = round($height / $width * $resize);

            $dst = imagecreatetruecolor($new_width, $new_height);
            imagecopyresized($dst, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            $image = $dst;
        }

        $newimg = imagewebp($image, $destination . $newName, $quality);
        return $newimg;
    }
}
