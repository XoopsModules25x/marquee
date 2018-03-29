<?php

use XoopsModules\Marquee;

require_once __DIR__ . '/admin_header.php';
// require_once __DIR__ . '/../class/helper.php';
//require_once __DIR__ . '/../include/common.php';
$helper = Marquee\Helper::getInstance();


$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');


$adminmenu[] = [
    'title' => _MI_MARQUEE_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png'
];

$adminmenu[] = [
    'title' => _MI_MARQUEE_MENU_01,
    'link'  => 'admin/main.php',
    'icon'  => $pathIcon32 . '/marquee.png'
];

$adminmenu[] = [
    'title' => _MI_MARQUEE_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png'
];
