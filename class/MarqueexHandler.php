<?php namespace XoopsModules\Marquee;

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
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

//require_once XOOPS_ROOT_PATH . '/kernel/object.php';
//require_once XOOPS_ROOT_PATH . '/modules/marquee/class/Utility.php';
//if (!class_exists('MarqueePersistableObjectHandler')) {
//    require_once XOOPS_ROOT_PATH . '/modules/marquee/class/PersistableObjectHandler.php';
//}



//class MarqueeHandler extends MarqueePersistableObjectHandler

/**
 * Class MarqueeHandler
 */
class MarqueexHandler extends \XoopsPersistableObjectHandler //MarqueePersistableObjectHandler
{
    /**
     * @param $db
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'marquee', Marqueex::class, 'marquee_marqueeid');
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
        foreach ($tbl_marquee as $oneMarquee) {
            $selected = '';
            if ($oneMarquee->getVar('marquee_marqueeid') == $selectedmarquee) {
                $selected = ' selected';
            }
            $content = '' !== xoops_trim(strip_tags($oneMarquee->getVar('marquee_content'))) ? xoops_substr(strip_tags($oneMarquee->getVar('marquee_content')), 0, 50) : $oneMarquee->getVar('marquee_source');
            $ret     .= '<option ' . $selected . " value='" . $oneMarquee->getVar('marquee_marqueeid') . "'>" . $content . '</option>';
        }

        return $ret;
    }

    /**
     * Quickly insert a record like this $myobjectHandler->quickInsert('field1' => field1value, 'field2' => $field2value)
     *
     * @param array $vars  Array containing the fields name and value
     * @param bool  $force whether to force the query execution despite security settings
     *
     * @return bool @link insert's value
     */
    public function quickInsert($vars = null, $force = false)
    {
        $object = $this->create(true);
        $object->setVars($vars);
        $retval = $this->insert($object, $force);
        unset($object);

        return $retval;
    }
}
