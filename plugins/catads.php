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
 * @copyright         Hervé Thouzard (http://www.herve-thouzard.com)
 * @license           http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package           marquee
 * @author            Hervé Thouzard (http://www.herve-thouzard.com)
 *
 * Version : $Id:
 * ****************************************************************************
 *
 * @param $limit
 * @param $dateformat
 * @param $itemssize
 *
 * @return array
 */

// Script to list recent ads from the catads module (tested with catads v 1.4)
function b_marquee_catads($limit, $dateformat, $itemssize)
{
    global $xoopsModule, $xoopsModuleConfig, $xoopsDB;
    include_once XOOPS_ROOT_PATH . '/modules/catads/class/cat.php';
    $block = array();
    if (empty($xoopsModule) || $xoopsModule->getVar('dirname') !== 'catads') {
        $module_handler = xoops_getHandler('module');
        $module         = $module_handler->getByDirname('catads');
        $config_handler = xoops_getHandler('config');
        $config         =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
    } else {
        $module =& $xoopsModule;
        $config =& $xoopsModuleConfig;
    }
    //echo '<br />ok';
    $ads_hnd  = xoops_getModuleHandler('ads', 'catads');
    $criteria = new CriteriaCompo(new Criteria('waiting', '0'));
    $criteria->add(new Criteria('published', time(), '<'));
    $criteria->add(new Criteria('expired', time(), '>'));
    $criteria->setSort('published');
    $criteria->setOrder('DESC');
    $criteria->setLimit($options[0]);
    $nbads = $ads_hnd->getCount($criteria);

    $a_item     = array();
    $cat_buffer = array();

    if ($nbads > 0) {
        $ads = $ads_hnd->getObjects($criteria);
        $ts  = MyTextSanitizer::getInstance();
        foreach ($ads as $oneads) {
            if ($itemssize > 0) {
                $title = xoops_substr($oneads->getVar('ads_title'), 0, $itemssize);
            } else {
                $title = $oneads->getVar('ads_title');
            }
            if (!isset($cat_buffer[$oneads->getVar('cat_id')])) {
                $tmpcat                                = new AdsCategory($oneads->getVar('cat_id'));
                $cat_buffer[$oneads->getVar('cat_id')] = $tmpcat->title();
                $cat_title                             = $tmpcat->title();
            } else {
                $cat_title = $cat_buffer[$oneads->getVar('cat_id')];
            }
            $block[] = array(
                'date'     => formatTimestamp($oneads->getVar('published'), $dateformat),
                'category' => '',
                'author'   => XoopsUser::getUnameFromId($oneads->getVar('uid')),
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/catads/adsitem.php?ads_id=' . $oneads->getVar('ads_id') . "'>" . $title . '</a>');
            unset($a_item);
        }
    }

    return $block;
}
