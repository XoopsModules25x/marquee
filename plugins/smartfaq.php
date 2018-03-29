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

// Script to list recent FAQ from the smartfaq module (tested with smartfaq 1.04)
function b_marquee_smartfaq($limit, $dateFormat, $itemsSize)
{
    require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';
    $block = [];

    $smartModule       =& sf_getModuleInfo();
    $smartModuleConfig =& sf_getModuleConfig();

    $categoryid        = -1;
    $sort              = 'datesub';
    $maxQuestionLength = 99999;
    if ($itemsSize > 0) {
        $maxQuestionLength = $itemsSize;
    }

    // Creating the faq handler object
    /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
    $faqHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Faq');
    // Creating the category handler object
    /** @var \XoopsModules\Smartfaq\CategoryHandler $categoryHandler */
    $categoryHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Category');

    // Creating the last FAQs
    $faqsObj       = $faqHandler->getAllPublished($limit, 0, $categoryid, $sort);
    $allcategories = $categoryHandler->getObjects(null, true);
    if ($faqsObj) {
        $userids = $faqids = [];
        foreach ($faqsObj as $key => $thisfaq) {
            $faqids[]                 = $thisfaq->getVar('faqid');
            $userids[$thisfaq->uid()] = 1;
        }
        /** @var \XoopsModules\Smartfaq\AnswerHandler $answerHandler */
        $answerHandler = \XoopsModules\Smartfaq\Helper::getInstance()->getHandler('Answer');
        $allanswers    = $answerHandler->getLastPublishedByFaq($faqids);

        foreach ($allanswers as $key => $thisanswer) {
            $userids[$thisanswer->uid()] = 1;
        }

        $memberHandler = xoops_getHandler('member');
        $users         = $memberHandler->getUsers(new \Criteria('uid', '(' . implode(',', array_keys($userids)) . ')', 'IN'), true);
        for ($i = 0, $iMax = count($faqsObj); $i < $iMax; ++$i) {
            $answerObj = $allanswers[$faqsObj[$i]->faqid()];
            $title     = $faqsObj[$i]->question($maxQuestionLength);
            $block[]   = [
                'date'     => $faqsObj[$i]->datesub(),
                'category' => $allcategories[$faqsObj[$i]->categoryid()]->getVar('name'),
                'author'   => sf_getLinkedUnameFromId($answerObj->uid(), $smartModuleConfig['userealname'], $users),
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/smartfaq/faq.php?faqid=' . $faqsObj[$i]->faqid() . "'>" . $title . '</a>'
            ];
        }
    }

    return $block;
}
