<?php defined( 'ABSPATH' ) or exit; ?>
<div class="wrap" id="stb-admin" class="stb-settings">

	<div class="stb-row">
		<div class="stb-col-two-third">

			<h2><?php _e( 'Settings', 'wp-heyloyalty' ); ?></h2>


			<form action="" method="post">

				<?php settings_fields( 'hl_settings' ); ?>
				<table class="form-table">
					<th><label for="hl_api_settings"><?php _e( 'Api settings', 'hl-api-settings' ); ?></label></th>
                    <tr>
                        <td colspan="2"><p><?php _e('Insert your api key and secret from your Heyloyalty account, you can find them under settings -> account information.', 'settings-intro') ?></p></td>
                    </tr>
                    <tr>
						<td width="10%">
                            <label><?php _e('Api key', 'hl_api_key'); ?></label>

						</td>
                        <td>
                            <input type="text" size="50" id="hl_api_key" name="hl_settings[api_key]" value="<?php if(isset($opts['api_secret'])) echo $opts['api_key']; ?>" />
                        </td>
					</tr>
                    <tr>
                        <td width="10%">
                            <label><?php _e('Api secret', 'hl_api_secret'); ?></label>
                        </td>
                        <td>
                            <input type="text" size="50" id="hl_api_secret" name="hl_settings[api_secret]" value="<?php if(isset($opts['api_secret'])) echo $opts['api_secret']; ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="hl_permission"><?php _e( 'Permission check', 'hl-permission' ); ?></label></th>
                    </tr>
                    <tr>
                        <td width="10%">
                            <label><?php _e('Permission', 'hl_permission'); ?></label>

                        </td>
                        <td>
<p><small>Whrite the name of the field you whish to use as the permission field, if the field is empty no check will be performed and all users will be sync to your selected list</small></p>
                            <input type="text" size="50" id="hl_permission" name="hl_settings[hl_permission]" value="<?php if(isset($opts['hl_permission'])) echo $opts['hl_permission']; ?>"/>

                        </td>
                    </tr>
				</table
				<?php submit_button(); ?>
			</form>
		</div>
        </div>
</div>