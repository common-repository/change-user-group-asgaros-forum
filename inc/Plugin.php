<?php

namespace Сoderun\Changegroupforum;

if (!defined('ABSPATH')) {
    exit;
}

class Plugin {

    protected static $_instance = null;

    /**
     * Singleton
     * @return Plugin
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function setEvents() {

        add_action('woocommerce_order_status_changed', array($this, 'order_status_changed'), 200, 4);
    }

    /**
     * Проверка зависимостей
     * @return boolean
     */
    public function checkAsgaros() {

        if (!method_exists('\AsgarosForumUserGroups', 'getUserGroups')) {
            return false;
        }

        if (!method_exists('\AsgarosForumUserGroups', 'getUserGroupsOfUser')) {
            return false;
        }

        if (!method_exists('\AsgarosForumUserGroups', 'insertUserGroupsOfUsers')) {
            return false;
        }


        return true;
    }

    /**
     * Включает пользователя в группу исходя из опций
     * @param type $order_id
     * @param type $old_status
     * @param type $new_status
     * @param type $order
     * @return type
     */
    public function order_status_changed($order_id, $old_status, $new_status, $order) {

        if ($old_status == $new_status) {
            return $order_id;
        }

        $instance = \Сoderun\Changegroupforum\Core::getInstance(); //core

        $options_name = $instance->getParamsPlugin()['prefix'] . 'options';


        $pluginOptions = get_option($options_name, [
            'order_group' => []//array - wc-processing:3556. (order_status:term_id_group_name)
        ]);

        $adgroup = [];

        foreach ($pluginOptions['order_group'] as $value) {
            $ar = explode(':', $value);
            $status = str_replace('wc-', '', $ar[0]);
            $term_group_id = $ar[1];

            if ($status == '-1' || $term_group_id == '-1') {
                continue;
            }

            if ($new_status === $status) {
                $adgroup [] = intval($term_group_id);
            }
        }

        if (empty($adgroup)) {
            return $order_id;
        }

        $user_id = $order->get_user_id();


        if (empty($user_id)) {
            return $order_id;
        }

        $curent_group = \AsgarosForumUserGroups::getUserGroupsOfUser($user_id, 'ids');


        if (empty($curent_group)) {
            $curent_group = [];
        }

        $group = array_merge($curent_group, $adgroup);

        if (!empty($group)) {
            \AsgarosForumUserGroups::insertUserGroupsOfUsers($user_id, $group);
        }

        return $order_id;
    }

    /**
     * Группы Asgaros Forum
     * @return array
     */
    public function getAsgarosGroupUser() {
        $_default = new \stdClass();
        $_default->term_id = '-1';
        $_default->parent = 0;
        $_default->name = __('No select group', 'coderun-asgaroswgroup');
        $_default->parent_name = __('Category user', 'coderun-asgaroswgroup');
        $default = [
            $_default
        ];

        $groups = \AsgarosForumUserGroups::getUserGroups();

        foreach ($groups as &$obj_item) {
            if (!empty($obj_item->parent)) {
                $obj_item->parent_name = $this->getTermName($obj_item->parent);
            }
        }

        return ($default + $groups);

        return $default;
    }

    /**
     * Статусы заказа WooCommerce
     * @return array
     */
    public function getWooStatus() {
        $default = [];
        if (function_exists('wc_get_order_statuses')) {
            return ($default + wc_get_order_statuses());
        }
        return $default;
    }

    protected function getTermName($term_id) {
        $obj = get_term_by('term_taxonomy_id', $term_id);

        if (!empty($obj)) {
            return $obj->name;
        }
        return null;
    }

    protected function __construct() {
        
    }

    public function __clone() {
        throw new \Exception('no method');
    }

    public function __wakeup() {
        throw new \Exception('no method');
    }

}
