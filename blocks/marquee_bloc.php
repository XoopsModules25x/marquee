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
 * @param $options
 *
 * @return array
 */

use XoopsModules\Marquee;

/**
 * @param $options
 * @return array
 */
function b_marquee_show($options)
{
    global $xoopsTpl;
    $marquee = null;
    //    require_once XOOPS_ROOT_PATH . '/modules/marquee/class/Utility.php';
    $marqueeHandler = XoopsModules\Marquee\Helper::getInstance()->getHandler('Marqueex');
    $block          = [];
    $marqueeId      = (int)$options[0];
    if ($marqueeId > 0) {
        /** @var Marquee\Marqueex $marquee */
        $marquee = $marqueeHandler->get($marqueeId);
        if (is_object($marquee)) {
            $uniqid = md5(uniqid(mt_rand(), true));
            if ('DHTML' === Marquee\Utility::getModuleOption('methodtouse')) {
                $link = '<script type="text/javascript" src="' . XOOPS_URL . '/modules/marquee/assets/js/xbMarquee.js"></script>';
                $link .= "\n<script type=\"text/javascript\">\n";
                $link .= 'var marquee' . $uniqid . ";\n";
                $link .= "var html$uniqid = '';\n";
                $link .= "function init_$uniqid()\n";
                $link .= "{\n";
                $link .= "\tmarquee$uniqid.start();\n";
                $link .= "}\n";
                $link .= "</script>\n";
                $xoopsTpl->assign('xoops_module_header', $link);
            }
            $block['marqueecode'] = $marquee->constructMarquee($uniqid);
        }
    }
    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function b_marquee_edit($options)
{
    /** @var Marquee\MarqueexHandler $marqueeHandler */
    $marqueeHandler = Marquee\Helper::getInstance()->getHandler('Marqueex');
    $form           = "<table border='0'>";
    $form           .= '<tr><td>' . _MB_MARQUEE_SELECT . "</td><td><select name='options[0]'>" . $marqueeHandler->getHtmlMarqueesList($options[0]) . '</select></td></tr>';
    $form           .= '</table>';
    return $form;
}

/*
 * Block on the fly
 */
/**
 * @param $options
 */
function b_marquee_custom($options)
{
    $options = explode('|', $options);
    $block   = b_marquee_show($options);
    $tpl     = new \XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:marquee_block.tpl');
}
