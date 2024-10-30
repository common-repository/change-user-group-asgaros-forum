<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
    <?php foreach (Сoderun\Changegroupforum\Core::getInstance()->getTabOptions() as $uri => $arTab) { ?>
    <a class="nav-tab <?php Сoderun\Changegroupforum\Core::getInstance()->adminActiveTab($uri); ?>" href="<?php echo add_query_arg(array('page' => Сoderun\Changegroupforum\Core::getInstance()->getParamsPlugin()['url_admin_menu'], 'tab' => $uri), 'admin.php'); ?>"><span class="<?php echo $arTab['ICON'] ?>"></span><?php echo $arTab['NAME']; ?></a>
        <?php } ?>
</h2>
<?php Сoderun\Changegroupforum\Core::getInstance()->tabViwer(); //Показать страницу в зависимости от закладки  ?>

