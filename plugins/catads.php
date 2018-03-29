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
 * Version :
 * ****************************************************************************
 *
 * @param $limit
 * @param $dateFormat
 * @param $itemsSize
 *
 * @return array
 */

use XoopsModules\Catads;

// Script to list recent ads from the catads module (tested with catads v 1.4)
/**
 * @param $limit
 * @param $dateFormat
 * @param $itemsSize
 * @return array
 */
function b_marquee_catads($limit, $dateFormat, $itemsSize)
{
    global $xoopsModule, $xoopsModuleConfig, $xoopsDB;
    require_once XOOPS_ROOT_PATH . '/modules/catads/class/cat.php';
    $block = [];
    if (empty($xoopsModule) || 'catads' !== $xoopsModule->getVar('dirname')) {
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->getByDirname('catads');
        $configHandler = xoops_getHandler('config');
        $config        = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
    } else {
        $module = $xoopsModule;
        $config = $xoopsModuleConfig;
    }
    //echo '<br>ok';
    $ads_hnd  = Catads\Helper::getInstance()->getHandler('Ads');
    $criteria = new \CriteriaCompo(new \Criteria('waiting', '0'));
    $criteria->add(new \Criteria('published', time(), '<'));
    $criteria->add(new \Criteria('expired', time(), '>'));
    $criteria->setSort('published');
    $criteria->setOrder('DESC');
    $criteria->setLimit($limit);
    $nbads = $ads_hnd->getCount($criteria);

    $itemArray = [];
    $catBuffer = [];

    if ($nbads > 0) {
        $ads  = $ads_hnd->getObjects($criteria);
        $myts = \MyTextSanitizer::getInstance();
        foreach ($ads as $oneads) {
            if ($itemsSize > 0) {
                $title = xoops_substr($oneads->getVar('ads_title'), 0, $itemsSize);
            } else {
                $title = $oneads->getVar('ads_title');
            }
            if (!isset($catBuffer[$oneads->getVar('cat_id')])) {
                $tmpcat                               = new Catads\AdsCategory($oneads->getVar('cat_id'));
                $catBuffer[$oneads->getVar('cat_id')] = $tmpcat->title();
                $catTitle                             = $tmpcat->title();
            } else {
                $catTitle = $catBuffer[$oneads->getVar('cat_id')];
            }
            $block[] = [
                'date'     => formatTimestamp($oneads->getVar('published'), $dateFormat),
                'category' => '',
                'author'   => \XoopsUser::getUnameFromId($oneads->getVar('uid')),
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/catads/adsitem.php?ads_id=' . $oneads->getVar('ads_id') . "'>" . $title . '</a>'
            ];
            unset($itemArray);
        }
    }

    return $block;
}
