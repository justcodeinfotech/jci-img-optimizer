<?php include_once 'components/header.php'; ?>
<div class="wc-body">
    <div class="wc-box wc-p25">
        <div class="wc-form">
            <form method="POST" id="wc-webp-config">
                <span class="wc-heading"><?php echo __('General settings', 'jci-webp-compressor') ?></span><br><br>
                <div class="wc-form-fields">
                    <div>
                        <label><?php echo __('Image Quality', 'jci-webp-compressor') ?></label>
                        <input type="text" class="wc-field" name="img_quality" value="<?php echo esc_html_e(isset($config['img_quality']) ? $config['img_quality'] : ''); ?>">
                    </div>
                    <div>
                        <label><?php echo __('Image Resize', 'jci-webp-compressor') ?></label>
                        <input type="text" class="wc-field" name="img_resize" value="<?php echo esc_html_e(isset($config['img_resize']) ? $config['img_resize'] : ''); ?>">
                    </div>
                </div>
                <?php wp_nonce_field('wc_general_settings_nonce', 'wc_general_settings_nonce'); ?>
                <input type="hidden" name="form_base" value="img_config">
                <input type="submit" class="wc-btn" value="Update">
            </form>
        </div>
    </div>
</div>