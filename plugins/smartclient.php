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

// Script to list recent clients from the smartclient module (tested with smartclient 1.02)
function b_marquee_smartclient($limit, $dateformat, $itemssize)
{
    $block = array();
    if (!defined('SMARTCLIENT_DIRNAME')) {
        define('SMARTCLIENT_DIRNAME', 'smartclient');
    }
    include_once(XOOPS_ROOT_PATH . '/modules/' . SMARTCLIENT_DIRNAME . '/include/common.php');

    // Creating the client handler object
    $client_handler =& smartclient_gethandler('client');

    $clientsObj =& $client_handler->getClients($limit, 0, _SCLIENT_STATUS_ACTIVE, 'title', 'ASC');
    if ($clientsObj) {
        for ($i = 0; $i < count($clientsObj); ++$i) {
            if ($itemssize > 0) {
                $title = xoops_substr($clientsObj[$i]->title(), 0, $itemssize);
            } else {
                $title = $clientsObj[$i]->title();
            }
            $block[] = array(
                'date'     => '',
                'category' => '',
                'author'   => '',
                'title'    => $title,
                'link'     => "<a href='" . XOOPS_URL . '/modules/smartclient/client.php?id=' . $clientsObj[$i]->id() . "'>" . $title . '</a>');
        }
    }

    return $block;
}
