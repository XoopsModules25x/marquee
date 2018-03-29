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

// Script to list recent partners from the smartpartner module (tested with smartparnter 1.2)
function b_marquee_smartpartner($limit, $dateFormat, $itemsSize)
{
    $block = $newObjects = [];
    if (!defined('SMARTPARTNER_DIRNAME')) {
        define('SMARTPARTNER_DIRNAME', 'smartpartner');
    }
    require_once XOOPS_ROOT_PATH . '/modules/' . SMARTPARTNER_DIRNAME . '/include/common.php';

    // Creating the partner handler object
    $smartpartnerPartnerHandler  = smartpartner_gethandler('partner');
    $smartpartnerCategoryHandler = smartpartner_gethandler('category');

    // Randomize
    $partnersObj = $smartpartnerPartnerHandler->getPartners(0, 0, _SPARTNER_STATUS_ACTIVE);
    if (count($partnersObj) > 1) {
        $keyArray = array_keys($partnersObj);
        $keyRand  = array_rand($keyArray, count($keyArray));
        for ($i = 0; ($i < count($partnersObj)) && ($i < $limit); ++$i) {
            $newObjects[$i] = $partnersObj[$keyRand[$i]];
        }
        $partnersObj = $newObjects;
    }
    $catId = [];
    foreach ($partnersObj as $partnerObj) {
        if (!in_array($partnerObj->categoryid(), $catId)) {
            $catId[] = $partnerObj->categoryid();
        }
    }

    if ($partnersObj) {
        foreach ($catId as $j => $jValue) {
            $categoryObj = $smartpartnerCategoryHandler->get($catId[$j]);
            for ($i = 0, $iMax = count($partnersObj); $i < $iMax; ++$i) {
                if ($partnersObj[$i]->categoryid() == $jValue) {
                    $smartConfig = smartpartner_getModuleConfig();
                    if ($itemsSize > 0) {
                        $title = xoops_substr($partnersObj[$i]->title(), 0, $itemsSize + 3);
                    } else {
                        $title = $partnersObj[$i]->title();
                    }

                    $block[] = [
                        'date'     => '',
                        'category' => '',
                        'author'   => '',
                        'title'    => $title,
                        'link'     => "<a href='" . XOOPS_URL . '/modules/smartpartner/partner.php?id=' . $partnersObj[$i]->id() . "'>" . $title . '</a>'
                    ];
                }
            }
        }
    }

    return $block;
}
