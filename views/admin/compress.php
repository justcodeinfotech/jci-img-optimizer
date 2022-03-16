<?php
// Get count of all attechments in library
$query_total_images_args = array(
    'post_type'      => 'attachment',
    'post_mime_type' => 'image',
    'post_status'    => 'inherit',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true,
);

$query_total_images = new WP_Query($query_total_images_args);
$total_images = count($query_total_images->posts);

// Get count of all attechments in library
$query_optimized_images_args = array(
    'post_type'      => 'attachment',
    'post_mime_type' => 'image',
    'post_status'    => 'inherit',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true,
    'meta_query'    => array(
        array(
            'key'       => 'jci_wc_optimized',
            'value'     => '1',
            'compare'   => '=',
        )
    )
);

$query_optimized_images = new WP_Query($query_optimized_images_args);
$optimized_images = count($query_optimized_images->posts);

if ($total_images == '') {
    $total_images = 0;
}
if ($optimized_images == '') {
    $optimized_images = 0;
}

include_once 'components/header.php'; ?>
<div class="wc-body">

    <div class="wc-box wc-p25">
        <div class="wc-optimize-status wc-flex wc-center">
            <div class="wc-optimize-chart-wrap">
                <div class="wc-mb5">
                    <span style="font-size: 16px;letter-spacing: 1px;"><?php echo __('Images optimize status', 'jci-webp-compressor') ?></span>
                </div>
                <div id="wc-optimize-chart" data-chart='0' data-total="<?php echo $total_images ?>" data-optimized="<?php echo esc_html($optimized_images) ?>"></div>
                <div class="optimized-chart-label">
                    <ul>
                        <li class="wc-p5"><span style="background: #3730A3;"></span><?php echo __('Optimized', 'jci-webp-compressor') ?></li>
                        <li class="wc-p5"><span style="background: #60A5FA;"></span><?php echo __('None Optimized', 'jci-webp-compressor') ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="wc-form">
            <form method="POST" id="wc-webp-optimize">
                <?php wp_nonce_field('wc_optimize_nonce', 'wc_optimize_nonce');
                if ($total_images != $optimized_images) {
                    echo ' <button type="submit" class="wc-btn">
                    <span class="button__text">'.__('Optimize now', 'jci-webp-compressor').'</span>
                </button>';
                }
                ?>

            </form>
        </div>
    </div>
</div>