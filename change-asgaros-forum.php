<?php
/*
 * Plugin Name: Change user group asgaros forum
 * Plugin URI: https://wordpress.org/plugins/change-user-group-asgaros-forum/
 * Description: The plugin links woocommerce order Statuses to Asgaros Forum User groups
 * Version: 1.1
 * Author: Djo
 * Author URI: https://zixn.ru
 * Text Domain: coderun-asgaroswgroup
 * Domain Path: /languages
 */

/*  Copyright 2020  Djo  (email: izm@zixn.ru)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'change_user_group_asgaros_forum');

/**
 * Plugin loader
 * @return object class()
 */
function change_user_group_asgaros_forum() {

    require_once (WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)) . '/inc/Core.php');

    $core = Сoderun\Changegroupforum\Core::getInstance();

    if (!$core->is_plugin_active_to_site('asgaros-forum') || !$core->is_plugin_active_to_site('woocommerce')) {

        add_action('admin_notices', function() {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>plugin: <strong>Change user group asgaros forum</strong></p>
                <p><?php _e('For the plugin to work, you must first activate the necessary plugins', 'coderun-asgaroswgroup'); ?></p>
                <p>WooCommerce and Asgaros forum</p>
            </div>
            <?php
        });

        return null;
    }

    $core->includePluginFunction(); //require logic

    $plugin = Сoderun\Changegroupforum\Plugin::getInstance(); //logic and function

    if ($plugin->checkAsgaros() === false) {
        add_action('admin_notices', function() {
            ?>
            <div class="notice notice-error is-dismissible">
                <p>plugin: <strong>Change user group asgaros forum</strong></p>
                <p><?php _e('The plug-in version is outdated because there have been changes in dependent plug-ins. Expect the plugin to be updated!', 'coderun-asgaroswgroup'); ?></p>
            </div>
            <?php
        });

        return null;
    }

    $plugin->setEvents(); //Обработка событий

    if (is_admin()) {
        $core->setOptionsPage();
    }

    if (is_ajax()) {
        $core->setAjaxEvents();
    }

    $GLOBALS['coderun_acgw'] = $core;

    register_deactivation_hook(__FILE__, array($core, 'deactivation_plugin'));

    return $core;
}
?>