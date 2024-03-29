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

// Script to list recent articles from the Smartsection module (tested with Smartsection 2.1)
function b_marquee_smartsection($limit, $dateFormat, $itemsSize)
{
    require_once XOOPS_ROOT_PATH . '/modules/smartsection/include/common.php';
    xoops_load('xoopsuserutility');
    $myts                    = \MyTextSanitizer::getInstance();
    $smartModule             = smartsection_getModuleInfo();
    $block                   = [];
    $categoryid              = -1;
    $sort                    = 'datesub';
    $order                   = smartsection_getOrderBy($sort);
    $smartsectionItemHandler = smartsection_gethandler('item');
    $itemsObj                = $smartsectionItemHandler->getAllPublished($limit, 0, $categoryid, $sort, $order);
    $totalItems              = count($itemsObj);
    if ($itemsObj) {
        for ($i = 0; $i < $totalItems; ++$i) {
            if ($itemsSize > 0) {
                $title = xoops_substr($itemsObj[$i]->title(), 0, $itemsSize + 3);
            } else {
                $title = $itemsObj[$i]->title();
            }
            $block[] = [
                'date'     => $itemsObj[$i]->datesub(),
                'category' => $itemsObj[$i]->getCategoryName(),
                'author'   => \XoopsUserUtility::getUnameFromId($itemsObj[$i]->uid()),
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/smartsection/item.php?itemid=' . $itemsObj[$i]->itemid() . "'>" . $title . '</a>',
            ];
        }
    }
    return $block;
}
