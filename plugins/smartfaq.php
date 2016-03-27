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

// Script to list recent FAQ from the smartfaq module (tested with smartfaq 1.04)
function b_marquee_smartfaq($limit, $dateformat, $itemssize)
{
    include_once(XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php');
    $block = array();

    $smartModule       =& sf_getModuleInfo();
    $smartModuleConfig =& sf_getModuleConfig();

    $categoryid        = -1;
    $sort              = 'datesub';
    $maxQuestionLength = 99999;
    if ($itemssize > 0) {
        $maxQuestionLength = $itemssize;
    }

    // Creating the faq handler object
    $faq_handler =& sf_gethandler('faq');

    // Creating the category handler object
    $category_handler =& sf_gethandler('category');

    // Creating the last FAQs
    $faqsObj       = $faq_handler->getAllPublished($limit, 0, $categoryid, $sort);
    $allcategories = $category_handler->getObjects(null, true);
    if ($faqsObj) {
        $userids = array();
        foreach ($faqsObj as $key => $thisfaq) {
            $faqids[]                 = $thisfaq->getVar('faqid');
            $userids[$thisfaq->uid()] = 1;
        }
        $answer_handler =& sf_gethandler('answer');
        $allanswers     = $answer_handler->getLastPublishedByFaq($faqids);

        foreach ($allanswers as $key => $thisanswer) {
            $userids[$thisanswer->uid()] = 1;
        }

        $member_handler = xoops_getHandler('member');
        $users          = $member_handler->getUsers(new Criteria('uid', '(' . implode(',', array_keys($userids)) . ')', 'IN'), true);
        for ($i = 0; $i < count($faqsObj); ++$i) {
            $answerObj =& $allanswers[$faqsObj[$i]->faqid()];
            $title     = $faqsObj[$i]->question($maxQuestionLength);
            $block[]   = array(
                'date'     => $faqsObj[$i]->datesub(),
                'category' => $allcategories[$faqsObj[$i]->categoryid()]->getVar('name'),
                'author'   => sf_getLinkedUnameFromId($answerObj->uid(), $smartModuleConfig['userealname'], $users),
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/smartfaq/faq.php?faqid=' . $faqsObj[$i]->faqid() . "'>" . $title . '</a>');
        }
    }

    return $block;
}
