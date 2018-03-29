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

use XoopsModules\Newbb;

// Script to list recent posts from Newbb 1 & 2
/**
 * @param $limit
 * @param $dateFormat
 * @param $itemsSize
 * @return array|false
 */
function b_marquee_newbb($limit, $dateFormat, $itemsSize)
{
//    require_once XOOPS_ROOT_PATH . '/modules/marquee/class/Utility.php';
    $block = [];

    /** @var \XoopsModuleHandler $moduleHandler */
    $moduleHandler = xoops_getHandler('module');
    $newbb         = $moduleHandler->getByDirname('newbb');
    $newbbVersion  = (int)$newbb->getInfo('version');

    if ($newbbVersion >= 2) {
        $order        = 't.topic_time';
        $forumHandler = Newbb\Helper::getInstance()->getHandler('Forum');
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $newbb         = $moduleHandler->getByDirname('newbb');

        if (null === $newbbConfig) {
            /** @var \XoopsConfigHandler $configHandler */
            $configHandler = xoops_getHandler('config');
            $newbbConfig   = $configHandler->getConfigsByCat(0, $newbb->getVar('mid'));
        }

        if (null === $access_forums) {
            $access_forums = $forumHandler->getForums(0, 'access'); // get all accessible forums
        }
        $validForums   = array_keys($access_forums);
        $forumCriteria = ' AND t.forum_id IN (' . implode(',', $validForums) . ')';
        unset($access_forums);
        $approveCriteria = ' AND t.approved = 1 AND p.approved = 1';
        $db              = \XoopsDatabaseFactory::getDatabaseConnection();
        $query           = 'SELECT t.*, f.forum_name, f.allow_subject_prefix, p.post_id, p.icon, p.uid, p.poster_name, u.uname, u.name FROM '
                           . $db->prefix('bb_topics')
                           . ' t, '
                           . $db->prefix('bb_forums')
                           . ' f, '
                           . $db->prefix('bb_posts')
                           . ' p LEFT JOIN '
                           . $db->prefix('users')
                           . ' u ON u.uid = p.uid WHERE f.forum_id=t.forum_id '
                           . $forumCriteria
                           . $approveCriteria
                           . ' AND t.topic_last_post_id=p.post_id ORDER BY '
                           . $order
                           . ' DESC';
        $result          = $db->query($query, $limit, 0);
        if (!$result) {
            return false;
        }
        $rows = [];
        while (false !== ($row = $db->fetchArray($result))) {
            $rows[] = $row;
        }
        if (count($rows) < 1) {
            return false;
        }
        $myts = \MyTextSanitizer::getInstance();

        foreach ($rows as $arr) {
            $title = $myts->htmlSpecialChars($arr['topic_title']);
            if ($itemsSize > 0) {
                $title = xoops_substr($title, 0, $itemsSize + 3);
            }

            $block[] = [
                'date'     => formatTimestamp($arr['topic_time'], $dateFormat),
                'category' => $arr['forum_name'],
                'author'   => $arr['uid'],
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/newbb/viewtopic.php?topic_id=' . $arr['topic_id'] . '&amp;post_id=' . $arr['post_id'] . '#forumpost' . $arr['post_id'] . "'>" . $title . '</a>'
            ];
        }
    } else { // Newbb 1
        $db    = \XoopsDatabaseFactory::getDatabaseConnection();
        $myts  = \MyTextSanitizer::getInstance();
        $order = 't.topic_time';
        $time  = $tmpuser = '';
        $query = 'SELECT t.topic_id, t.topic_title, t.topic_last_post_id, t.topic_time, t.topic_views, t.topic_replies, t.forum_id, f.forum_name FROM '
                 . $db->prefix('bb_topics')
                 . ' t, '
                 . $db->prefix('bb_forums')
                 . ' f WHERE f.forum_id=t.forum_id AND f.forum_type <> 1 ORDER BY '
                 . $order
                 . ' DESC';
        if (!$result = $db->query($query, $limit, 0)) {
            return false;
        }
        while (false !== ($arr = $db->fetchArray($result))) {
            $lastpostername = $db->query('SELECT post_id, uid FROM ' . $db->prefix('bb_posts') . ' WHERE post_id = ' . $arr['topic_last_post_id']);
            while (false !== ($tmpdb = $db->fetchArray($lastpostername))) {
                $tmpuser = XoopsUser::getUnameFromId($tmpdb['uid']);
                $time    = formatTimestamp($arr['topic_time'], $dateFormat);
            }
            $title = $myts->htmlSpecialChars($arr['topic_title']);
            if ($itemsSize > 0) {
                $title = xoops_substr($title, 0, $itemsSize + 3);
            }

            $block[] = [
                'date'     => $time,
                'category' => $arr['forum_name'],
                'author'   => $tmpuser,
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/newbb/viewtopic.php?topic_id=' . $arr['topic_id'] . '&amp;forum=' . $arr['forum_id'] . '&amp;post_id=' . $arr['topic_last_post_id'] . '#forumpost' . $arr['topic_last_post_id'] . "'>" . $title . '</a>'
            ];
        }
    }

    return $block;
}
