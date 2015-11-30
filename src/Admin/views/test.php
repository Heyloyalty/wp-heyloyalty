<?php defined('ABSPATH') or exit; ?>
<div class="wrap" id="stb-admin" class="stb-settings">
    <div class="stb-row">
        <div class="stb-col-two-third">
            <h2><?php _e('Test page', 'wp-heyloyalty'); ?>
                <form action="" method="post">
                    <table class="form-table">
                        <th>
                            Time
                        </th>
                        <th>
                            Event type
                        </th>
                        <th>
                            Message
                        </th>

                                <?php foreach($status as $key => $value) : ?>
                                    <tr>
                                    <td><?php echo str_replace('entry-','',$key);?></td><td><?php echo $value['type']; ?></td><td><?php echo $value['message'];?></td>
                            </tr>
                                <?php endforeach; ?>

                        <input type="hidden" name="identity" value="test-page" />
                    </table
                    <?php submit_button(); ?>
                </form>
        </div>
    </div>
</div>