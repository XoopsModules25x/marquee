<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

use XoopsModules\Marquee;

$moduleDirName = basename(dirname(__DIR__));

require_once __DIR__ . '/../class/Helper.php';
require_once __DIR__ . '/../class/Utility.php';
require_once __DIR__ . '/../class/Marquee.php';
require_once __DIR__ . '/../class/MarqueeHandler.php';

$db     = \XoopsDatabaseFactory::getDatabase();
$helper = Marquee\Helper::getInstance();

/** @var \XoopsModules\Marquee\Utility $utility */
$utility = new Marquee\Utility();

if (!defined('MARQUEE_MODULE_PATH')) {
    define('MARQUEE_DIRNAME', basename(dirname(__DIR__)));
    define('MARQUEE_URL', XOOPS_URL . '/modules/' . MARQUEE_DIRNAME);
    define('MARQUEE_IMAGE_URL', MARQUEE_URL . '/assets/images/');
    define('MARQUEE_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . MARQUEE_DIRNAME);
    define('MARQUEE_IMAGE_PATH', MARQUEE_ROOT_PATH . '/assets/images');
    define('MARQUEE_ADMIN_URL', MARQUEE_URL . '/admin/');
    define('MARQUEE_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . MARQUEE_DIRNAME);
    define('MARQUEE_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . MARQUEE_DIRNAME);
}

$helper->loadLanguage('common');

//require_once MARQUEE_ROOT_PATH . '/class/Utility.php';
//require_once MARQUEE_ROOT_PATH . '/include/constants.php';
//require_once MARQUEE_ROOT_PATH . '/include/seo_functions.php';
//require_once MARQUEE_ROOT_PATH . '/class/metagen.php';
//require_once MARQUEE_ROOT_PATH . '/class/session.php';
//require_once MARQUEE_ROOT_PATH . '/class/xoalbum.php';
//require_once MARQUEE_ROOT_PATH . '/class/request.php';

$pathIcon16    = \Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32    = \Xmf\Module\Admin::iconUrl('', 32);
$pathModIcon16 = $helper->getModule()->getInfo('modicons16');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$icons = [
    'edit'   => "<img src='" . $pathIcon16 . "/edit.png'  alt=" . _EDIT . "' align='middle'>",
    'delete' => "<img src='" . $pathIcon16 . "/delete.png' alt='" . _DELETE . "' align='middle'>",
    'clone'  => "<img src='" . $pathIcon16 . "/editcopy.png' alt='" . _CLONE . "' align='middle'>",
    'preview' => "<img src='" . $pathIcon16 . "/view.png' alt='" . _PREVIEW . "' align='middle'>",

    'print'   => "<img src='" . $pathIcon16 . "/printer.png' alt='" . _CLONE . "' align='middle'>",
    'pdf'     => "<img src='" . $pathIcon16 . "/pdf.png' alt='" . _CLONE . "' align='middle'>",
//    'add'     => "<img src='" . $pathIcon16 . "/add.png' alt='" . _ADD . "' align='middle'>",
//    '0'     => "<img src='" . $pathIcon16 . "/0.png' alt='" . _ADD . "' align='middle'>",
//    '1'     => "<img src='" . $pathIcon16 . "/1.png' alt='" . _ADD . "' align='middle'>",
];

$debug = false;

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof \XoopsTpl)) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    $xoopsTpl = new \XoopsTpl();
}

//module URL for templates
$GLOBALS['xoopsTpl']->assign('mod_url', XOOPS_URL . '/modules/' . $moduleDirName);

// Local icons path
$GLOBALS['xoopsTpl']->assign('pathModIcon16', XOOPS_URL . '/modules/' . $moduleDirName . '/' . $pathModIcon16);
$GLOBALS['xoopsTpl']->assign('pathModIcon32', $pathModIcon32);

//module handlers

/** @var PluginHandler $pluginHandler */
$pluginHandler  = $helper->getHandler('plugin');
$marquee        = new Marquee\Marquee();
$marqueeHandler = new Marquee\MarqueeHandler($db);
