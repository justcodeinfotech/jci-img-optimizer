<?php include_once 'components/header.php'; ?>
<div class="wc-body">
    <div class="wc-box wc-p25">
        <div class="wc-form">
            <form method="POST" id="wc-webp-config">
                <span class="wc-heading"><?php echo __('General settings', 'jci-webp-compressor') ?></span><br><br>
                <div class="wc-form-fields quality">
                    <label><?php echo __('Image Quality', 'jci-webp-compressor') ?></label>
                    <div class="select-img-wrap">
                        <div class="select-img-quality <?php echo (isset($img_quality) && $img_quality == 100) ? 'active' : '' ?>">
                            <input type="radio" id="img-quality-normal" name="img_quality" value="100" <?php echo (isset($img_quality) && $img_quality == 100) ? 'checked' : '' ?>>
                            <label for="img-quality-normal"><?php echo __('Normal', 'jci-webp-compressor') ?></label>
                        </div>
                        <div class="select-img-quality <?php echo (isset($img_quality) && $img_quality == 95) ? 'active' : '' ?>">
                            <input type="radio" id="img-quality-medium" name="img_quality" value="95" <?php echo (isset($img_quality) && $img_quality == 95) ? 'checked' : '' ?>>
                            <label for="img-quality-medium"><?php echo __('Medium', 'jci-webp-compressor') ?></label>
                        </div>
                        <div class="select-img-quality <?php echo (isset($img_quality) && $img_quality == 90) ? 'active' : '' ?>">
                            <input type="radio" id="img-quality-high" name="img_quality" value="90" <?php echo (isset($img_quality) && $img_quality == 90) ? 'checked' : '' ?>>
                            <label for="img-quality-high"><?php echo __('High', 'jci-webp-compressor') ?></label>
                        </div>
                        <div class="select-img-quality <?php echo (isset($img_quality) && $img_quality == 85) ? 'active' : '' ?>">
                            <input type="radio" id="img-quality-ultra" name="img_quality" value="85" <?php echo (isset($img_quality) && $img_quality == 85) ? 'checked' : '' ?>>
                            <label for="img-quality-ultra"><?php echo __('Ultra', 'jci-webp-compressor') ?></label>
                        </div>
                        <div class="select-img-quality <?php echo (isset($img_quality) && $img_quality == 80) ? 'active' : '' ?>">
                            <input type="radio" id="img-quality-extream" name="img_quality" value="80" <?php echo (isset($img_quality) && $img_quality == 80) ? 'checked' : '' ?>>
                            <label for="img-quality-extream"><?php echo __('Extream', 'jci-webp-compressor') ?></label>
                        </div>
                    </div>
                </div>
                <div class="wc-form-fields">
                    <div>
                        <label><?php echo __('Image Resize', 'jci-webp-compressor') ?></label>
                        <input type="text" class="wc-field" name="img_resize" value="<?php echo esc_html_e(isset($img_resize) ? $img_resize : ''); ?>">
                    </div>
                </div>
                <?php wp_nonce_field('wc_general_settings_nonce', 'wc_general_settings_nonce'); ?>
                <input type="hidden" name="form_base" value="img_config">
                <input type="submit" class="wc-btn" value="Update">
            </form>
        </div>
    </div>
</div>