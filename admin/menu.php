<?php

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

//$path = dirname(dirname(dirname(__DIR__)));
//include_once $path . '/mainfile.php';
//
//$dirname         = basename(dirname(__DIR__));
//$module_handler  = xoops_getHandler('module');
//$module          = $module_handler->getByDirname($dirname);
//$pathIcon32      = $module->getInfo('icons32');
//$pathModuleAdmin = $module->getInfo('dirmoduleadmin');
//$pathLanguage    = $path . $pathModuleAdmin;
//
//if (!file_exists($fileinc = $pathLanguage . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/' . 'main.php')) {
//    $fileinc = $pathLanguage . '/language/english/main.php';
//}
//
//include_once $fileinc;

$module_handler = xoops_getHandler('module');
$module         = $module_handler->getByDirname(basename(dirname(__DIR__)));
$pathIcon32     = '../../' . $module->getInfo('icons32');
xoops_loadLanguage('modinfo', $module->dirname());

$pathModuleAdmin = XOOPS_ROOT_PATH . '/' . $module->getInfo('dirmoduleadmin') . '/moduleadmin';
if (!file_exists($fileinc = $pathModuleAdmin . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/' . 'main.php')) {
    $fileinc = $pathModuleAdmin . '/language/english/main.php';
}
include_once $fileinc;

//$adminmenu = array();

$adminmenu[] = array(
    'title' => _AM_MODULEADMIN_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png');

$adminmenu[] = array(
    'title' => _MI_MARQUEE_MENU_01,
    'link'  => 'admin/main.php',
    'icon'  => $pathIcon32 . '/marquee.png');

$adminmenu[] = array(
    'title' => _AM_MODULEADMIN_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png');
