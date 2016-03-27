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
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

include_once XOOPS_ROOT_PATH . '/kernel/object.php';
include_once XOOPS_ROOT_PATH . '/modules/marquee/include/functions.php';
if (!class_exists('MarqueePersistableObjectHandler')) {
    include_once XOOPS_ROOT_PATH . '/modules/marquee/class/PersistableObjectHandler.php';
}

//class Marquee extends XoopsObject
/**
 * Class Marquee
 */
class Marquee extends XoopsObject
{
    /**
     * marquee constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('marquee_marqueeid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_direction', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_scrollamount', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_behaviour', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_bgcolor', XOBJ_DTYPE_TXTBOX, null, false, 7);
        $this->initVar('marquee_align', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_height', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_width', XOBJ_DTYPE_TXTBOX, null, false, 4);
        $this->initVar('marquee_hspace', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_scrolldelay', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_stoponmouseover', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_loop', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_vspace', XOBJ_DTYPE_INT, null, false);
        $this->initVar('marquee_content', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('marquee_source', XOBJ_DTYPE_TXTBOX, null, false, 255);
        // To be able to use html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1);
    }

    /**
     * @param string $uniqid
     *
     * @return mixed|string
     */
    public function constructmarquee($uniqid = '')
    {
        include_once XOOPS_ROOT_PATH . '/modules/marquee/include/functions.php';
        $tblalign     = array('top', 'bottom', 'middle');
        $tblbehaviour = array('scroll', 'slide', 'alternate');
        $tbldirection = array('right', 'left', 'up', 'down');
        $stop         = $this->getVar('marquee_stoponmouseover') == 1 ? ' onmouseover="this.stop()" onmouseout="this.start()"' : '';
        $bgcolor      = trim($this->getVar('marquee_bgcolor')) != '' ? " bgcolor='" . $this->getVar('marquee_bgcolor') . "'" : '';
        $height       = $this->getVar('marquee_height') != 0 ? ' height=' . $this->getVar('marquee_height') : '';
        $hspace       = $this->getVar('marquee_hspace') != 0 ? ' hspace=' . $this->getVar('marquee_hspace') : '';
        $width        = trim($this->getVar('marquee_width')) != '' ? " width='" . $this->getVar('marquee_width') . "'" : '';
        $scrolldelay  = $this->getVar('marquee_scrolldelay') != 0 ? ' scrolldelay=' . $this->getVar('marquee_scrolldelay') : '';
        $loop         = $this->getVar('marquee_loop') != 0 ? ' loop=' . $this->getVar('marquee_loop') : " loop='infinite'";
        $vspace       = $this->getVar('marquee_vspace') != 0 ? ' vspace=' . $this->getVar('marquee_vspace') : '';
        $scrollamount = $this->getVar('marquee_scrollamount') != 0 ? ' scrollamount=' . $this->getVar('marquee_scrollamount') : '';
        $br           = ' - ';

        if ($this->getVar('marquee_direction') > 1) {
            $br = '<br />';
        }

        $content = '';
        if ($this->getVar('marquee_source') !== 'fixed') {
            include_once XOOPS_ROOT_PATH . '/modules/marquee/plugins/' . $this->getVar('marquee_source') . '.php';
            $function_name = 'b_marquee_' . $this->getVar('marquee_source'); // For example b_marquee_comments
            if (function_exists($function_name)) {
                $limit      = marquee_getmoduleoption('itemscount');
                $dateformat = marquee_getmoduleoption('dateformat');
                $itemssize  = marquee_getmoduleoption('itemssize');
                $retval     = call_user_func($function_name, $limit, $dateformat, $itemssize);
                if (is_array($retval) && count($retval) > 0) {
                    foreach ($retval as $onevalue) {
                        if (isset($onevalue['category']) && xoops_trim($onevalue['category']) != '') {
                            $onevalue['category'] = ' - ' . $onevalue['category'];
                        }
                        if (isset($onevalue['link']) && xoops_trim($onevalue['link']) != '') {
                            $onevalue['link'] = ' - ' . $onevalue['link'];
                        }
                        $content .= $onevalue['date'] . $onevalue['category'] . $onevalue['link'] . $br;
                    }
                }
            }
        } else {
            $content = $this->getVar('marquee_content');
        }
        if (!marquee_isbot()) { // We are using the microsoft html tag
            if (strtolower(marquee_getmoduleoption('methodtouse')) !== 'dhtml') {
                return "<marquee align='" . $tblalign[$this->getVar('marquee_align')] . "' behavior='" . $tblbehaviour[$this->getVar('marquee_behaviour')] . "' direction='" . $tbldirection[$this->getVar('marquee_direction')] . "' " . $stop . $scrollamount . $bgcolor . $height . $hspace . $width . $scrolldelay . $loop . $vspace . '>' . $content . '</marquee>';
            } else { // We are using the javascript method
                $jscontent = '';
                $jscontent .= "<script type=\"text/javascript\">\n";
                $jscontent .= "html$uniqid = '';\n";
                $jscontent .= "html$uniqid += '" . marquee_javascript_escape($content) . "' ;\n";
                $jscontent .= "marquee$uniqid = new XbMarquee('marquee$uniqid', " . $this->getVar('marquee_height') . ', ' . $this->getVar('marquee_width') . ', ' . $this->getVar('marquee_scrollamount') . ', ' . $this->getVar('marquee_scrolldelay') . ", '" . $tbldirection[$this->getVar('marquee_direction')] . "', '" . $tblbehaviour[$this->getVar('marquee_behaviour')] . "', html$uniqid);\n";
                $jscontent .= "init_$uniqid();\n";
                $jscontent .= "</script>\n";

                return $jscontent;
            }
        } else {
            return $content;
        }
    }
}

//class MarqueeMarqueeHandler extends MarqueePersistableObjectHandler
/**
 * Class MarqueeMarqueeHandler
 */
class MarqueeMarqueeHandler extends MarqueePersistableObjectHandler
{
    /**
     * @param $db
     */
    public function __construct($db)
    {
        parent::__construct($db, 'marquee', 'marquee', 'marquee_marqueeid');
    }

    /**
     * @param int $selectedmarquee
     *
     * @return string
     */
    public function getHtmlMarqueesList($selectedmarquee = 0)
    {
        $ret         = '';
        $tbl_marquee =& $this->getObjects();
        foreach ($tbl_marquee as $one_marquee) {
            $selected = '';
            if ($one_marquee->getVar('marquee_marqueeid') == $selectedmarquee) {
                $selected = ' selected';
            }
            $content = xoops_trim(strip_tags($one_marquee->getVar('marquee_content'))) != '' ? xoops_substr(strip_tags($one_marquee->getVar('marquee_content')), 0, 50) : $one_marquee->getVar('marquee_source');
            $ret .= '<option ' . $selected . " value='" . $one_marquee->getVar('marquee_marqueeid') . "'>" . $content . '</option>';
        }

        return $ret;
    }
}
