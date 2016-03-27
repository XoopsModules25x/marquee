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
 * @version            $Id $
 * ****************************************************************************
 *
 * @param $limit
 * @param $dateformat
 * @param $itemssize
 *
 * @return array
 */

// Script to list the recent links from the mydownloads module version 1.10
function b_marquee_mydownloads($limit, $dateformat, $itemssize)
{
    include_once XOOPS_ROOT_PATH . '/modules/marquee/include/functions.php';
    include_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
    $block  = array();
    $myts   = MyTextSanitizer::getInstance();
    $db     = XoopsDatabaseFactory::getDatabaseConnection();
    $result = $db->query('SELECT m.lid, m.cid, m.title, m.date, m.hits, m.submitter, c.title as catitle, u.name, u.uname FROM ' . $db->prefix('mydownloads_downloads') . ' m, ' . $db->prefix('mydownloads_cat') . '  c, ' . $db->prefix('users') . ' u  WHERE (c.cid=m.cid) AND (m.submitter=u.uid) AND (m.status>0) ORDER BY m.date DESC', $limit, 0);
    while ($myrow = $db->fetchArray($result)) {
        $title = $myts->htmlSpecialChars($myrow['title']);
        if ($itemssize > 0) {
            $title = xoops_substr($title, 0, $itemssize + 3);
        }
        $author = $myts->htmlSpecialChars($myrow['uname']);
        if (xoops_trim($myrow['catitle']) != '') {
            $author = $myts->htmlSpecialChars($myrow['name']);
        }
        $category = $myts->htmlSpecialChars($myrow['catitle']);
        $block[]  = array(
            'date'     => formatTimestamp($myrow['date'], $dateformat),
            'category' => $category,
            'author'   => $author,
            'title'    => $title,
            'link'     => "<a href='" . XOOPS_URL . '/modules/mydownloads/singlefile.php?cid=' . $myrow['cid'] . '&amp;lid=' . $myrow['lid'] . "'>" . $title . '</a>');
    }

    return $block;
}
