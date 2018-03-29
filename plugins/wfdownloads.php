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

use XoopsModules\Wfdownloads;

// Script to list recent files from the wfdownloads module (tested with wfdownloads 3.1)
/**
 * @param $limit
 * @param $dateFormat
 * @param $itemsSize
 * @return array
 */
function b_marquee_wfdownloads($limit, $dateFormat, $itemsSize)
{
    $block = [];

    global $xoopsUser;
    $moduleHandler  = xoops_getHandler('module');
    $wfModule       = $moduleHandler->getByDirname('wfdownloads');
    $configHandler  = xoops_getHandler('config');
    $wfModuleConfig = $configHandler->getConfigsByCat(0, $wfModule->getVar('mid'));

    $groups       = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $gpermHandler = xoops_getHandler('groupperm');
    $allowed_cats = $gpermHandler->getItemIds('WFDownCatPerm', $groups, $wfModule->getVar('mid'));

    $criteria = new \Criteria('cid', '(' . implode(',', $allowed_cats) . ')', 'IN');
    $criteria = new \CriteriaCompo(new \Criteria('offline', 0));
    $criteria->setSort('published');
    $criteria->setOrder('DESC');
    $criteria->setLimit($limit);
    $downloadHandler = Wfdownloads\Helper::getInstance()->getHandler('Download');
    $categoryHandler = Wfdownloads\Helper::getInstance()->getHandler('Category');
    $buffer_category = [];

    $downloads = $downloadHandler->getObjects($criteria);

    foreach (array_keys($downloads) as $i) {
        $download = $downloads[$i]->toArray();
        if ($itemsSize > 0) {
            $title = xoops_substr($download['title'], 0, $itemsSize);
        } else {
            $title = $download['title'];
        }
        if (isset($buffer_category[$download['cid']])) {
            $categtitle = $buffer_category[$download['cid']];
        } else {
            $category   = $categoryHandler->get($download['cid']);
            $categtitle = $buffer_category[$download['cid']] = $category->getVar('title');
        }
        $block[] = [
            'date'     => formatTimestamp($download['published'], $wfModuleConfig['dateformat']),
            'category' => $categtitle,
            'author'   => $download['publisher'],
            'title'    => $title,
            'link'     => "<a href='" . XOOPS_URL . '/modules/wfdownloads/singlefile.php?cid=' . $download['cid'] . '&lid=' . $download['lid'] . "'>" . $title . '</a>'
        ];
    }

    return $block;
}
