<?php
if (!defined('ABSPATH')) {
    exit;
}

$instance = Сoderun\Changegroupforum\Core::getInstance(); //core

$options_name = $instance->getParamsPlugin()['prefix'] . 'options';


$pluginOptions = get_option($options_name, [
    'order_group' => []//array - wc-processing:3556. (order_status:term_id_group_name)
        ]); //Массив настроек

$plugin = Сoderun\Changegroupforum\Plugin::getInstance(); //base function


?>
<h3><?php
    _e('Set the order status match the forum group. '
            . 'When the order switches to the specified status, '
            . 'the forum group will be assigned to the user who placed the order.', 'coderun-asgaroswgroup');
    ?>
</h3>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
    <div class="table-responsive">
        <table class="table">
            <td>
<?php foreach ($plugin->getWooStatus() as $key_status => $woo_status) { ?>
                    <fieldset>
                        <legend><strong><?php _e('Status order', 'coderun-asgaroswgroup') ?></strong>: <?php echo $woo_status; ?></legend>
                        <select name="<?php echo $options_name; ?>[order_group][]">
                            <?php foreach ($plugin->getAsgarosGroupUser() as $key_group => $obj_value) { ?>
                                <?php
                                $key_option = $key_status . ':' . $obj_value->term_id;
                                ?>
                                <option <?php selected((in_array($key_option, $pluginOptions['order_group'])), true, true); ?> value="<?php echo $key_option; ?>"><?php echo $obj_value->parent_name; ?>: <?php echo $obj_value->name; ?></option>
                    <?php } ?>
                        </select>
                    </fieldset>
<?php } ?>

            </td>
        </table>
    </div>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="<?php echo $options_name; ?>" />
    <p class="submit">
        <input type="submit" class="button-primary save_form_options" value="<?php _e('Save Changes') ?>" />
    </p>
</form>

