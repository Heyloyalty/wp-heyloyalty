<?php defined('ABSPATH') or exit; ?>
<style>
    .form-table tr td.notification {
    border-left:4px solid cornflowerblue;
     padding: 5px;

 }
.form-table tr td.type span {
    background-color: gray;
     color:#ffffff;
     padding:6px;
 }
.form-table tr td .error span {
    background-color: darkred;
 }
.form-table tr td .created span {
    background-color: darkgreen;
 }
.form-table tr td .updated span {
    background-color: darkblue;
 }
.form-table tr td .deleted span {
    background-color: darkgoldenrod;
 }
.form-table tr.row {
    background-color: #ffffff;
 }
.form-table tr.spacer {
     height: 5px;
 }
</style>
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
<?php foreach ($status as $key => $value) : ?>
     <tr class="row">
     <td class="notification"><?php echo str_replace('entry-', '', $key); ?></td>
         <td class="type <?php echo $value['type']; ?>"><span><?php echo $value['type']; ?></span></td><td><?php echo $value['message']; ?></td></tr>
             <tr class="spacer"></tr>
<?php endforeach; ?>
     </table>
     </td>
     </tr>
     </table>
     </div>
