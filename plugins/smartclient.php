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

// Script to list recent clients from the smartclient module (tested with smartclient 1.02)
function b_marquee_smartclient($limit, $dateFormat, $itemsSize)
{
    $block = [];
    if (!defined('SMARTCLIENT_DIRNAME')) {
        define('SMARTCLIENT_DIRNAME', 'smartclient');
    }
    require_once XOOPS_ROOT_PATH . '/modules/' . SMARTCLIENT_DIRNAME . '/include/common.php';

    // Creating the client handler object
    $clientHandler = smartclient_gethandler('client');

    $clientsObj = $clientHandler->getClients($limit, 0, _SCLIENT_STATUS_ACTIVE, 'title', 'ASC');
    if ($clientsObj) {
        for ($i = 0, $iMax = count($clientsObj); $i < $iMax; ++$i) {
            if ($itemsSize > 0) {
                $title = xoops_substr($clientsObj[$i]->title(), 0, $itemsSize);
            } else {
                $title = $clientsObj[$i]->title();
            }
            $block[] = [
                'date'     => '',
                'category' => '',
                'author'   => '',
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/smartclient/client.php?id=' . $clientsObj[$i]->id() . "'>" . $title . '</a>'
            ];
        }
    }

    return $block;
}
