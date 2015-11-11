<?php defined( 'ABSPATH' ) or exit; ?>
<div class="wrap" id="stb-admin" class="stb-settings">

	<div class="stb-row">
		<div class="stb-col-two-third">

			<h2><?php _e( 'Mappings', 'wp-heyloyalty' ); ?></h2>

			<form action="" method="post">
				<?php settings_fields( 'hl_mappings' ); ?>
				<table class="form-table">
					<th><label for="hl_api_settings"><?php _e( 'List mappings', 'hl-list-mappings' ); ?></label></th>

                    <tr>
                        <td><label><?php _e('Choose a list', 'choose-list'); ?></label></td>
                        <td>
                            <select id="hl_lists" name="hl_mappings[list_id]">
                                <option><?php _e('select list', 'select-list'); ?></option>
                                <?php foreach($lists as $list) : ?>
                                    <option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="16%"><h4>User fields</h4></td>
                        <td><h4>List fields</h4></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="wp-container">
                                <label>Name:<div class="droppable "></div></label>
                                <label>Last name:<div class="droppable "></div></label>
                                <label>Mobile:<div class="droppable "></div></label>
                                <label>Postalcode:<div class="droppable "></div></label>
                                <label>City:<div class="droppable "></div></label>
                                <label>Address:<div class="droppable "></div></label>
                                <label>Address 2:<div class="droppable "></div></label>
                                <label>Token:<div class="droppable "></div></label>
                                <label>Billing address:<div class="droppable "></div></label>
                                <label>Shipping address:<div class="droppable "></div></label>
                                <label>Shipping city:<div class="droppable "></div></label>
                                <label>Shipping postalcode:<div class="droppable "></div></label>
                                <label>Shipping mobile:<div class="droppable "></div></label>
                                <label>Post owner:<div class="droppable "></div></label>
                                <label>Admin:<div class="droppable "></div></label>
                                <label>Billing mobile:<div class="droppable "></div></label>
                                <label>Has energy:<div class="droppable "></div></label>
                                <label>Billing City:<div class="droppable "></div></label>
                            </div>
                            <div class="hl-container">
                                <div class="draggable"><label>Name</label><img style="float:right; margin:3px 3px;" src="<?php echo $this->plugin->url().'/assets/img/badge_cancel_32.png'; ?>"/></div>
                                <div class="draggable">Last name<img style="float:right; margin:3px 3px;" src="<?php echo $this->plugin->url().'/assets/img/badge_cancel_32.png'; ?>"/></div>
                                <div class="draggable">Mobil<img style="float:right; margin:3px 3px;" src="<?php echo $this->plugin->url().'/assets/img/badge_cancel_32.png'; ?>"/></div>
                                <div class="draggable">Postalcode<img style="float:right; margin:3px 3px;" src="<?php echo $this->plugin->url().'/assets/img/badge_cancel_32.png'; ?>"/></div>
                                <div class="draggable">City<img style="float:right; margin:3px 3px;" src="<?php echo $this->plugin->url().'/assets/img/badge_cancel_32.png'; ?>"/></div>
                            </div>
                        </td>
                    </tr>
				</table
				<?php submit_button(); ?>
			</form>
		</div>
        </div>
</div>