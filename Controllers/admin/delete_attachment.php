<?php
if (!class_exists('jci_wc_delete_attachment')) {
    class jci_wc_delete_attachment
    {
        public function __construct()
        {
        }

        public function delete_attachment($attachment_id)
        {
            $file_path = wp_get_original_image_path($attachment_id); //image path
            $file_name = basename($file_path); // filename
            $folder_path =  str_replace($file_name, "", $file_path); // file-Folder path


            $ext = pathinfo($file_name, PATHINFO_EXTENSION); // get extension
            $img_file_name =  str_replace('.' . $ext, '', $file_name); // file name without extension


            unlink($folder_path . $img_file_name . '.webp'); // delete webp formate of orignal file
            unlink($folder_path . $img_file_name . '-scaled.webp'); // delete webp formate of scaled image

            $metadata = wp_get_attachment_metadata($attachment_id);
            if (!empty($metadata['sizes'])) {
                // Wordpress all images 
                $sizearr = $metadata['sizes'];

                foreach ($sizearr as $compressed_img) {
                    $image_name = isset($compressed_img['file']) ? $compressed_img['file'] : '';
                    if ($image_name == '')
                        continue;

                    $ext = pathinfo($image_name, PATHINFO_EXTENSION); // get extension
                    $img_file_name =  str_replace('.' . $ext, '', $image_name); // file name without extension
                    unlink($folder_path . $img_file_name . '.webp'); // remove the compressed images 
                }
            }
        }
    }
}
