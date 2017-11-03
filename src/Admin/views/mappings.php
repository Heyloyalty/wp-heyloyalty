<?php defined('ABSPATH') or exit; ?>
<div class="wrap" id="stb-admin" class="stb-settings">

    <div class="stb-row">
        <div class="stb-col-two-third">

            <h2><?php _e('Mappings', 'wp-heyloyalty'); ?>

                <?php if (is_array($lists) && count($lists) > 0) : ?>

                    <form action="" method="post">
                        <?php settings_fields('hl_mappings'); ?>
                        <table class="form-table">
                            <th colspan="2">
                                <label for="hl_api_settings"><?php _e('List mappings', 'hl-list-mappings'); ?></label>
                                <div><p>Here you can map your wordpress fields to a Heyloyalty list, if you have woocommerce installed you will see more fields.</p></div>
                            </th>

                            <tr>
                                <td><label><?php _e('Choose a list from your Heyloyalty account', 'choose-list'); ?></label></td>
                                    <td>
                                    <select id="hl_lists" name="hl_mappings[list_id]">
                                        <option><?php _e('select list', 'select-list'); ?></option>
                                        <?php foreach (json_decode($lists['response'],true) as $list) : ?>
                                            <option
                                                value="<?php echo $list['id']; ?>" <?php echo $selected = (isset($mappings['list_id']) && $list['id'] == $mappings['list_id']) ? 'selected' : '' ?>><?php echo $list['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                               <td colspan="2">
                                   <h3>Heyloyalty list fields</h3>
                                   <div class="hl-container">

                                    </div>
                               </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <h3>Wordpress user fields</h3>
                                    <div class="wp-container">
                                        <?php foreach ($user_fields as $key): ?>
                                            <label style="float:left;"><?php echo $key; ?>:
                                                <div class="droppable " data-name="<?php echo $key?>">
                                                    <?php if(isset($mappings['fields'][$key])) : ?>
                                                        <div class="draggable"><label><?php echo $mappings['fields'][$key]; ?></label></div>
                                                <?php endif; ?>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" name="mapped" value="" id="mapped-fields" />
                                </td>
                            </tr>
                        </table
                        <?php submit_button(); ?>
                    </form>
                <?php else: ?>
                    <div id="setting-error-settings_error" class="error settings-error notice is-dismissible">
                        <p>Your api key or secret is wrong!</p>
                        <button type="button" class="notice-dismiss"></button>
                    </div>
                <?php endif; ?>
        </div>
    </div>
</div>
