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
 *
 * @param $limit
 * @param $dateFormat
 * @param $itemsSize
 *
 * @return array
 */

// Script to list the recent links from the mylinks module version 1.10
function b_marquee_mylinks($limit, $dateFormat, $itemsSize)
{
//    require_once XOOPS_ROOT_PATH . '/modules/marquee/class/Utility.php';
    require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
    $block  = [];
    $myts   = \MyTextSanitizer::getInstance();
    $db     = \XoopsDatabaseFactory::getDatabaseConnection();
    $result = $db->query('SELECT m.lid, m.cid, m.title, m.date, m.hits, m.submitter, c.title AS catitle FROM ' . $db->prefix('mylinks_links') . ' m, ' . $db->prefix('mylinks_cat') . ' c WHERE (c.cid=m.cid) AND (m.status>0) ORDER BY date DESC', $limit, 0);
    while (false !== ($myrow = $db->fetchArray($result))) {
        $title = $myts->htmlSpecialChars($myrow['title']);
        if ($itemsSize > 0) {
            $title = xoops_substr($title, 0, $itemsSize + 3);
        }
        $block[] = [
            'date'     => formatTimestamp($myrow['date'], $dateFormat),
            'category' => $myts->htmlSpecialChars($myrow['catitle']),
            'author'   => $myrow['submitter'],
            'title'    => $title,
            'link'     => "<a href='" . XOOPS_URL . '/modules/mylinks/singlelink.php?cid=' . $myrow['cid'] . '&amp;lid=' . $myrow['lid'] . "'>" . $title . '</a>'
        ];
    }

    return $block;
}
