<?php defined('ABSPATH') or exit; ?>
<div class="wrap" id="stb-admin" class="stb-settings">
    <div class="stb-row">
        <div class="stb-col-two-third">
            <h2><?php _e('Woocommerce', 'wp-heyloyalty'); ?>
                <form action="" method="post">
                    <?php settings_fields('hl_woo'); ?>
                    <table class="form-table">
                        <th><label
                                for="hl_api_settings"><?php _e('Woocommerce settings', 'hl-woo-settings'); ?></label>
                        </th>

                        <tr>
                            <?php if('tumpe' === 'knold') : ?>
                            <td><label><?php _e('Abandon basket', 'abandonbasket'); ?></label></td>
                            <td>
                                <input type="checkbox" name="hl_woo[abandon]" <?php echo $checked = (isset($woo['abandon']) && $woo['abandon'] == 'on') ? 'checked' : '';?>/>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <td><label><?php _e('Product feeds', 'product-feeds'); ?><label></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="text" name="hl_woo[feed1]"  disabled size="50" value="<?php echo get_site_url()?>/shop/feed"/></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="text" name="hl_woo[feed2]" size="50"  disabled/></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="text" name="hl_woo[feed3]"  size="50" disabled/></td>
                        </tr>

                    </table
                    <?php submit_button(); ?>
                </form>
        </div>
    </div>
</div>