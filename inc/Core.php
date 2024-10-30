<?php

namespace Сoderun\Changegroupforum;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Базовый Класс
 * Создаёт административные разделы плагина, подключает скрипты, настройки т.д
 * Только базовый функционал активации плагина
 */
class Core {

    protected static $_instance = null;

    /**
     * Параметр конструктора
     * @var type 
     */
    protected $plugin = [];

    /**
     * Массив вкладок для страницы настроек
     * 
     * @var type 
     */
    protected $arOptionTabs = array(
        'general' => array(//часть uri вкладки
            'NAME' => ' Настройки', //Имя вкладки
            'PATCH' => '/admin/tab1.php', //Путь до подключаемого файла страницы настроек. Относительно папки плагина
            'ICON' => 'glyphicon glyphicon-cog', //Иконка для вкладки - класс иконки
        ),
    );

    protected function __construct() {

        $this->plugin = [
            'name' => 'Change user group asgaros forum',
            'folder' => 'change-user-group-asgaros-forum',
            'url_admin_menu' => 'change-user-group-asgaros-forum',
            'start_page_option' => 'admin/option1.php', //страница опций плагина
            'name_start_page' => 'Настройки', // Название титульной страницы плагина
            'name_menu' => 'Asgarosgroup',
            'url_control' => 'options-general.php?page=change-user-group-asgaros-forum', //Адрес админки плагина полный
            'prefix' => 'coderunasg', //Префикс для настроек
        ];

        $this->plugin['abs_folder'] = WP_PLUGIN_DIR . '/' . $this->plugin['folder'];

        $this->addActios();
    }

    public function getParamsPlugin() {
        return $this->plugin;
    }

    public function getTabOptions() {
        return $this->arOptionTabs;
    }

    /**
     * Singletone
     * @return Core
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __wakeup() {
        return false;
    }

    public function __clone() {
        return false;
    }

    protected function addActios() {
        
    }

    public function setOptionsPage() {
        add_action('admin_menu', array($this, 'adminOptions'));

        add_filter('plugin_action_links', array($this, 'pluginLinkSetting'), 10, 2); //Настройка на странице плагинов
    }

    public function setAjaxEvents() {
        require_once $this->plugin['abs_folder'] . '/inc/Ajax.php';
        new Ajax();
    }

    public function includePluginFunction() {
        require_once $this->plugin['abs_folder'] . '/inc/Plugin.php';
    }

    /**
     * Добавляет пункт настроек на странице активированных плагинов
     */
    public function pluginLinkSetting($links, $file) {
        $this_plugin = $this->plugin['folder'] . '/index.php';
        if ($file == $this_plugin) {
            $settings_link1 = '<a href="' . $this->plugin['url_control'] . '">' . __("Settings", "default") . '</a>';
            array_unshift($links, $settings_link1);
        }
        return $links;
    }

    public function deactivation_plugin() {
        return true;
    }

    /**
     * Параметры активируемого меню
     */
    public function adminOptions() {
        $page_option = add_options_page($this->plugin['name_start_page'], $this->plugin['name_menu'], 'activate_plugins', $this->plugin['url_admin_menu'], array($this, 'showSettingPage'));
        add_action('admin_print_styles-' . $page_option, array(self::getInstance(), 'syleScriptAddpage')); //загружаем стили только для страницы плагина
        add_action('admin_print_scripts-' . $page_option, array(self::getInstance(), 'scriptAddpage')); //Скрипты админки
    }

    /**
     * Стили, скрипты
     */
    public function syleScriptAddpage() {
        wp_register_style($this->plugin['prefix'] . '-adm', plugins_url() . '/' . $this->plugin['folder'] . '/' . 'css/adminpag.css');
        wp_enqueue_style($this->plugin['prefix'] . '-adm');
    }

    /**
     * Сприпты
     */
    public function scriptAddpage() {
        wp_register_script($this->plugin['prefix'] . '_admin', plugins_url() . '/' . $this->plugin['folder'] . '/' . 'js/admin_order.js');
        wp_enqueue_script($this->plugin['prefix'] . '_admin');
    }

    /**
     * Страница меню
     */
    public function showSettingPage() {
        include_once $this->plugin['abs_folder'] . '/' . $this->plugin['start_page_option'];
    }

    /**
     * Активная вкладка в админпанели плагина
     * @return string css Класс для активной вкладки
     */
    public function adminActiveTab($tab_name = null, $tab = null) {

        if (isset($_GET['tab']) && !$tab)
            $tab = $_GET['tab'];
        else
            $tab = 'general';

        $output = '';
        if (isset($tab_name) && $tab_name) {
            if ($tab_name == $tab)
                $output = ' nav-tab-active';
        }
        echo $output;
    }

    /**
     * Проверка активирован ли плагин на сайте
     * @param type $plugin_name
     * @return boolean
     */
    public function is_plugin_active_to_site($plugin_name) {
        $plugins = get_option('active_plugins', []);

        foreach ($plugins as $key => $value) {
            $arPatch = explode('/', $value);
            if ($arPatch[0] === $plugin_name) {
                return true;
            }
        }
        return false;
    }

    /**
     * Подключает нужную страницу исходя из вкладки на страницы настроек плагина
     * @result include_once tab{номер вкладки}-option1.php
     */
    public function tabViwer() {
        if (isset($_GET['tab'])) {
            $tab = $_GET['tab'];
        } else {
            $tab = 'general';
        }

        switch ($tab) {
            case 'general':
                include_once $this->plugin['abs_folder'] . '/' . $this->arOptionTabs[$tab]['PATCH'];
                break;
            case 'project':
                include_once $this->plugin['abs_folder'] . '/' . $this->arOptionTabs[$tab]['PATCH'];
                break;
            case 'jornal':
                include_once $this->plugin['abs_folder'] . '/' . $this->arOptionTabs[$tab]['PATCH'];
                break;
            case 'about':
                include_once $this->plugin['abs_folder'] . '/' . $this->arOptionTabs[$tab]['PATCH'];
                break;
            case 'margin':
                include_once $this->plugin['abs_folder'] . '/' . $this->arOptionTabs[$tab]['PATCH'];
                break;
            default :
                include_once $this->plugin['abs_folder'] . '/' . $this->arOptionTabs[$tab]['PATCH'];
                break;
        }
    }

}

?>