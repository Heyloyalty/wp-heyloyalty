<?php defined('ABSPATH') or exit; ?>
<div class="wrap">
    <table class="form-table">
        <tr>
            <td width="50%">
            <table class="form-table">
                <tr><th>Some other content</th></tr>
            </table>
            </td>
            <td>
                <h3>Status messages</h3>
            <table class="form-table">
                <tr><th>Time</th><th>Type</th><th>Message</th></tr>
                <?php foreach($status as $key => $value) : ?>
                <tr><td><?php echo str_replace('entry-','',$key);?></td><td><?php echo $value['type']; ?></td><td><?php echo $value['message']; ?></td></tr>
                <?php endforeach; ?>
            </table>
            </td>
        </tr>
    </table>
    </div>