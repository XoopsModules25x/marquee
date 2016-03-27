<?php
/**
 * ****************************************************************************
 * Marquee - MODULE FOR XOOPS
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

// Script to list recent articles from the xfsection module (tested with xfsection 1.12)
function b_marquee_xfsection($limit, $dateformat, $itemssize)
{
    $block = array();
    global $xoopsDB;
    if (!function_exists('xfblock_checkAccess')) {
        include_once XOOPS_ROOT_PATH . '/modules/xfsection/include/xfblock_groupaccess.php';
    }

    $myts   = MyTextSanitizer::getInstance();
    $sql    = 'SELECT articleid, title, published, expired, counter, groupid, uid FROM ' . $xoopsDB->prefix('xfs_article') . ' WHERE published < ' . time() . ' AND published > 0 AND (expired = 0 OR expired > ' . time() . ') AND noshowart = 0 AND offline = 0 ORDER BY published DESC';
    $result = $xoopsDB->query($sql, $limit, 0);
    while ($myrow = $xoopsDB->fetchArray($result)) {
        if (xfblock_checkAccess($myrow['groupid'])) {
            $wfs   = array();
            $title = $myts->htmlSpecialChars($myrow['title']);
            if (!XOOPS_USE_MULTIBYTES) {
                if ($itemssize > 0) {
                    $title = $myts->htmlSpecialChars(substr($myrow['title'], 0, $itemssize - 1));
                } else {
                    $title = $myts->htmlSpecialChars($myrow['title']);
                }
            }
            $block[] = array(
                'date'     => formatTimestamp($myrow['published'], $dateformat),
                'category' => '',
                'author'   => XoopsUser::getUnameFromId($myrow['uid']),
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/xfsection/article.php?articleid=' . $myrow['articleid'] . "'>" . $title . '</a>');
        }
    }

    return $block;
}
