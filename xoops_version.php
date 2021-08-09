<?php
/**
 * ****************************************************************************
 * marquee - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard (http://www.herve-thouzard.com)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright          Hervé Thouzard (http://www.herve-thouzard.com)
 * @license            http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package            marquee
 * @author             Hervé Thouzard (http://www.herve-thouzard.com)
 * ****************************************************************************
 */

require_once __DIR__ . '/preloads/autoloader.php';
$moduleDirName                     = basename(__DIR__);
$moduleDirNameUpper                = mb_strtoupper($moduleDirName);
$modversion['version']             = '2.61';
$modversion['module_status']       = 'Beta 1';
$modversion['release_date']        = '2021/08/08';
$modversion['name']                = _MI_MARQUEE_NAME;
$modversion['description']         = _MI_MARQUEE_DESC;
$modversion['credits']             = 'Carnuke, defkon1, the Newbb team, Mage, Mamba';
$modversion['author']              = 'Hervé Thouzard';
$modversion['nickname']            = 'hervet';
$modversion['help']                = 'page=help';
$modversion['license']             = 'GNU GPL 2.0';
$modversion['license_url']         = 'www.gnu.org/licenses/gpl-2.0.html';
$modversion['official']            = 0; //1 indicates supported by XOOPS Dev Team, 0 means 3rd party supported
$modversion['image']               = 'assets/images/logoModule.png';
$modversion['dirname']             = basename(__DIR__);
$modversion['modicons16']          = 'assets/images/icons/16';
$modversion['modicons32']          = 'assets/images/icons/32';
$modversion['module_website_url']  = 'www.xoops.org/';
$modversion['module_website_name'] = 'XOOPS';
$modversion['author_website_url']  = 'https://xoops.org/';
$modversion['author_website_name'] = 'Hervé Thouzard';
$modversion['min_php']             = '7.3';
$modversion['min_xoops']           = '2.5.10';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = ['mysql' => '5.5'];
//update
//$modversion['onUpdate'] = 'include/onupdate.php';
// SQL Tables
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][0]        = 'marquee';
// Admin menu
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu']  = 'admin/menu.php';
$modversion['hasMain']    = 0;
// Set to 1 if you want to display menu generated by system module
$modversion['system_menu'] = 1;
// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_MARQUEE_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_MARQUEE_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_MARQUEE_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_MARQUEE_SUPPORT, 'link' => 'page=support'],
];
// ------------------- Blocks ------------------- //
$modversion['blocks'][] = [
    'file'        => 'marquee_bloc.php',
    'name'        => _MI_MARQUEE_BNAME_01,
    'description' => _MI_MARQUEE_BDESC_01,
    'show_func'   => 'b_marquee_show',
    'options'     => '1',
    'edit_func'   => 'b_marquee_edit',
    'template'    => 'marquee_block01.tpl',
];
$modversion['blocks'][] = [
    'file'        => 'marquee_bloc.php',
    'name'        => _MI_MARQUEE_BNAME_02,
    'description' => _MI_MARQUEE_BDESC_02,
    'show_func'   => 'b_marquee_show',
    'options'     => '2',
    'edit_func'   => 'b_marquee_edit',
    'template'    => 'marquee_block02.tpl',
];
$modversion['blocks'][] = [
    'file'        => 'marquee_bloc.php',
    'name'        => _MI_MARQUEE_BNAME_03,
    'description' => _MI_MARQUEE_BDESC_03,
    'show_func'   => 'b_marquee_show',
    'options'     => '3',
    'edit_func'   => 'b_marquee_edit',
    'template'    => 'marquee_block03.tpl',
];
$modversion['blocks'][] = [
    'file'        => 'marquee_bloc.php',
    'name'        => _MI_MARQUEE_BNAME_04,
    'description' => _MI_MARQUEE_BDESC_04,
    'show_func'   => 'b_marquee_show',
    'options'     => '4',
    'edit_func'   => 'b_marquee_edit',
    'template'    => 'marquee_block04.tpl',
];
// Search
$modversion['hasSearch'] = 0;
// Options
/**
 * Editor to use (was usekiovi)
 */
xoops_load('xoopseditorhandler');
$editorHandler          = XoopsEditorHandler::getInstance();
$modversion['config'][] = [
    'name'        => 'form_options',
    'title'       => '_MI_MARQUEE_TEXT_EDITOR',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'tinymce',
    'options'     => array_flip($editorHandler->getList()),
];
/**
 * Which method to use to create the marquee (The Microsoft marque tag or the Javascript method ?)
 */
$modversion['config'][] = [
    'name'        => 'methodtouse',
    'title'       => '_MI_MARQUEE_METHOD',
    'description' => '_MI_MARQUEE_METHOD_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'Marquee',
    'options'     => ['_MI_MARQUEE_MARQUEE' => 'Marquee', '_MI_MARQUEE_DHTML' => 'DHTML'],
];
/**
 * Date's format (only use with plugins)
 */
$modversion['config'][] = [
    'name'        => 'dateformat',
    'title'       => '_MI_MARQUEE_DATEFORMAT',
    'description' => '_MI_MARQUEE_DATEFORMAT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '_SHORTDATESTRING',
];
/**
 * Count of items taken from other modules (only use with plugins)
 */
$modversion['config'][] = [
    'name'        => 'itemscount',
    'title'       => '_MI_MARQUE_ITEMSCOUNT',
    'description' => '_MI_MARQUE_ITEMSCOUNT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 0,
];
/**
 * Titles length (only use with plugins)
 */
$modversion['config'][] = [
    'name'        => 'itemssize',
    'title'       => '_MI_MARQUE_TITLELENGTH',
    'description' => '_MI_MARQUE_TITLELENGTH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 0,
];
/**
 * Make Sample button visible?
 */
$modversion['config'][] = [
    'name'        => 'displaySampleButton',
    'title'       => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_SAMPLE_BUTTON',
    'description' => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_SAMPLE_BUTTON_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];
/**
 * Show Developer Tools?
 */
$modversion['config'][] = [
    'name'        => 'displayDeveloperTools',
    'title'       => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_DEV_TOOLS',
    'description' => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_DEV_TOOLS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];
