<?php defined('ABSPATH') or exit; ?>
<div class="wrap" id="stb-admin" class="stb-settings">
    <div class="stb-row">
        <div class="stb-col-two-third">
            <h2><?php _e('Test page', 'wp-heyloyalty'); ?>
                <form action="" method="post">
                    <table class="form-table">
                        <th><label
                                for="hl_api_settings"><?php _e('Test settings', 'hl-woo-settings'); ?></label>
                        </th>
                        <tr><td>
                                <label>Testing...</label>
                            </td></tr>
                        <input type="hidden" name="identity" value="test-page" />
                    </table
                    <?php submit_button(); ?>
                </form>
        </div>
    </div>
</div>