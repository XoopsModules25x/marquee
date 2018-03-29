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

// Script to list system's comments - Tested with Xoops 2.0.9.3
function b_marquee_comments($limit, $dateFormat, $itemsSize)
{
//    require_once XOOPS_ROOT_PATH . '/modules/marquee/class/Utility.php';
    require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
    $block  = [];
    $status = XOOPS_COMMENT_APPROVEUSER;
    $module = 0;
    /** @var \XoopsModuleHandler $moduleHandler */
    $moduleHandler  = xoops_getHandler('module');
    /** @var \XoopsCommentHandler $commentHandler */
    $commentHandler = xoops_getHandler('comment');
    $criteria       = new \CriteriaCompo();
    if ($status > 0) {
        $criteria->add(new \Criteria('com_status', $status));
    }
    if ($module > 0) {
        $criteria->add(new \Criteria('com_modid', $module));
    }
    $total = $commentHandler->getCount($criteria);
    if ($total > 0) {
        $start = 0;
        $sort  = 'com_created';
        $order = 'DESC';
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $comments = $commentHandler->getObjects($criteria, true);
        foreach (array_keys($comments) as $i) {
            $module         = $moduleHandler->get($comments[$i]->getVar('com_modid'));
            $comment_config = $module->getInfo('comments');
            if ($itemsSize > 0) {
                $title = xoops_substr($comments[$i]->getVar('com_title'), 0, $itemsSize + 3);
            } else {
                $title = $comments[$i]->getVar('com_title');
            }
            $block[] = [
                'date'     => formatTimestamp($comments[$i]->getVar('com_created'), $dateFormat),
                'category' => '',
                'author'   => $comments[$i]->getVar('com_uid'),
                'title'    => $title,
                'link'     => "<a href='"
                              . XOOPS_URL
                              . '/modules/'
                              . $module->getVar('dirname')
                              . '/'
                              . $comment_config['pageName']
                              . '?'
                              . $comment_config['itemName']
                              . '='
                              . $comments[$i]->getVar('com_itemid')
                              . '&com_id='
                              . $comments[$i]->getVar('com_id')
                              . '&com_rootid='
                              . $comments[$i]->getVar('com_rootid')
                              . '&com_mode=thread&'
                              . str_replace('&amp;', '&', $comments[$i]->getVar('com_exparams'))
                              . '#comment'
                              . $comments[$i]->getVar('com_id')
                              . "'>"
                              . $title
                              . '</a>'
            ];
        }
    }

    return $block;
}
