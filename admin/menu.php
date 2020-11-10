<?php

use Xmf\Module\Admin;
use XoopsModules\Marquee\Helper;

require dirname(__DIR__) . '/preloads/autoloader.php';
$moduleDirName      = basename(dirname(__DIR__));
$moduleDirNameUpper = mb_strtoupper($moduleDirName);
$helper = Helper::getInstance();
$helper->loadLanguage('common');
$helper->loadLanguage('feedback');
$pathIcon32 = Admin::menuIconPath('');
if (is_object($helper->getModule())) {
    //    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');
    $pathModIcon32 = $helper->url($helper->getModule()->getInfo('modicons32'));
}
$adminmenu[] = [
    'title' => _MI_MARQUEE_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png',
];
$adminmenu[] = [
    'title' => _MI_MARQUEE_MENU_01,
    'link'  => 'admin/main.php',
    'icon'  => $pathIcon32 . '/marquee.png',
];
// Blocks Admin
$adminmenu[] = [
    'title' => constant('CO_' . $moduleDirNameUpper . '_' . 'BLOCKS'),
    'link'  => 'admin/blocksadmin.php',
    'icon'  => $pathIcon32 . '/block.png',
];
if (is_object($helper->getModule()) && $helper->getConfig('displayDeveloperTools')) {
    $adminmenu[] = [
        'title' => constant('CO_' . $moduleDirNameUpper . '_' . 'ADMENU_MIGRATE'),
        'link'  => 'admin/migrate.php',
        'icon'  => $pathIcon32 . '/database_go.png',
    ];
}
$adminmenu[] = [
    'title' => _MI_MARQUEE_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png',
];
