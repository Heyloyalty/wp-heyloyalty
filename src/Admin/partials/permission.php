<?php defined('ABSPATH') or exit; ?>
  <h3><?php _e("Heyloyalty Permission", "blank"); ?></h3>
  <table class="form-table">
    <tr>
      <th><label for="permission"><?php _e("Permission"); ?></label></th>
      <td>
        <input type="checkbox" name="hl_permission" id="permission" <?php echo $checked = (get_user_meta($userID,'hl_permission',true) == 'on') ? 'checked' : ''; ?> />
    </td>
    </tr>
  </table>