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
 * Version :
 * ****************************************************************************
 *
 * @param $limit
 * @param $dateFormat
 * @param $itemsSize
 *
 * @return array
 */

// Script to list recent articles from wfsection 1 & 2
function b_marquee_wfsection($limit, $dateFormat, $itemsSize)
{
//    require_once XOOPS_ROOT_PATH . '/modules/marquee/class/Utility.php';
    $block = [];

    $myts = \MyTextSanitizer::getInstance();
    /** @var \XoopsModuleHandler $moduleHandler */
    $moduleHandler    = xoops_getHandler('module');
    $wfsection        = $moduleHandler->getByDirname('wfsection');
    $wfsectionVersion = (int)$wfsection->getInfo('version');

    if ($wfsectionVersion >= 2) {
    } else { // wfsection 1
        require_once XOOPS_ROOT_PATH . '/modules/wfsection/include/groupaccess.php';
        global $xoopsDB;
        $sql    = 'SELECT articleid, title, published, expired, counter, groupid, uid FROM ' . $xoopsDB->prefix('wfs_article') . ' WHERE published < ' . time() . ' AND published > 0 AND (expired = 0 OR expired > ' . time() . ') AND noshowart = 0 AND offline = 0 ORDER BY published DESC';
        $result = $xoopsDB->query($sql, $limit, 0);
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            if (checkAccess($myrow['groupid'])) {
                $wfs   = [];
                $title = $myts->htmlSpecialChars($myrow['title']);
                if (!XOOPS_USE_MULTIBYTES) {
                    if ($itemsSize > 0) {
                        $title = $myts->htmlSpecialChars(substr($myrow['title'], 0, $itemsSize));
                    } else {
                        $title = $myts->htmlSpecialChars($myrow['title']);
                    }
                }
                $block[] = [
                    'date'     => formatTimestamp($myrow['published'], $dateFormat),
                    'category' => '',
                    'author'   => XoopsUser::getUnameFromId($myrow['uid']),
                    'title'    => $title,
                    'link'     => "<a href='" . XOOPS_URL . '/modules/wfsection/article.php?articleid=' . $myrow['articleid'] . "'>" . $title . '</a>'
                ];
            }
        }
    } // wfsection 1 ou 2 ?

    return $block;
}
