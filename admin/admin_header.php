<?php
/**
 * Marquee module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright           XOOPS Project (http://xoops.org)
 * @license             http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package             Marquee
 * @since               2.5.0
 * @author              XOOPS Module Team
 * @version             $Id $
 **/

$rootPath = dirname(dirname(dirname(__DIR__)));
include_once $rootPath . '/mainfile.php';
include_once $rootPath . '/include/cp_functions.php';
require_once $rootPath . '/include/cp_header.php';

global $xoopsModule;

$moduleFolder = dirname(__DIR__);

//if functions.php file exist
require_once $moduleFolder . '/include/functions.php';

// Load language files
xoops_loadLanguage('admin', $moduleFolder);
xoops_loadLanguage('modinfo', $moduleFolder);
xoops_loadLanguage('main', $moduleFolder);

$pathIcon16      = XOOPS_URL . '/' . $xoopsModule->getInfo('icons16');
$pathIcon32      = XOOPS_URL . '/' . $xoopsModule->getInfo('icons32');
$pathModuleAdmin = XOOPS_ROOT_PATH . '/' . $xoopsModule->getInfo('dirmoduleadmin');

require_once $pathModuleAdmin . '/moduleadmin/moduleadmin.php';
