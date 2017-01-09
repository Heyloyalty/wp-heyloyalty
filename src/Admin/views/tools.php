<?php defined('ABSPATH') or exit; ?>
<div class="wrap" id="stb-admin" class="stb-settings">
    <div class="stb-row">
        <div class="stb-col-two-third">

            <h2><?php _e('Tools', 'wp-heyloyalty'); ?>
                <form action="" method="post">
                    <table class="form-table">
                        <tr>
                            <td width="10%">
                                <select name="user">
                                    <option>Select user</option>
                                    <?php foreach ($users as $user) : ?>
                                        <option
                                            value="<?php echo $user->ID ?>"><?php echo $user->user_email ?></option>
                                    <?php endforeach; ?>

                                </select>
                            </td>
                            <td>
                                <select name="action">
                                    <option>Select action</option>
                                    <option value="create">Create</option>
                                    <option value="update">Update</option>
                                    <option value="delete">Delete</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Perform action'); ?>
                </form>
        </div>
    </div>
    <div class="stb-row">
        <div class="stb-col-two-third">

            <h2><?php _e('Shop, choice and multi fields explore', 'wp-heyloyalty'); ?></h2>
                <script> var fields = <?php echo $fields ?></script>
            <table class="form-table">
                <tr>
                    <td>
                        <div class="fields-container"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="fields-info-container">
                            <ul class="field-info" style="display:none;"></ul>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>