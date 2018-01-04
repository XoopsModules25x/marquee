<?php namespace XoopsModules\Marquee;

use Xmf\Request;
 use \XoopsModules\Marquee\Common;


require_once __DIR__ . '/../include/common.php';

/**
 * Class Utility
 */
class Utility
{
    use common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use common\ServerStats; // getServerStats Trait

    use common\FilesManagement; // Files Management Trait

    //--------------- Custom module methods -----------------------------
    const MODULE_NAME = 'marquee';

    /**
     * Access the only instance of this class
     *
     * @return \XoopsModules\Marquee\Utility
     *
     * @static
     * @staticvar   object
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Returns a module's option (with cache)
     *
     * @param string  $option    module option's name
     * @param boolean $withCache Do we have to use some cache ?
     *
     * @return mixed option's value
     */
    public static function getModuleOption($option, $withCache = true)
    {
        global $xoopsModuleConfig, $xoopsModule;
        $repmodule = self::MODULE_NAME;
        static $options = [];
        if (is_array($options) && array_key_exists($option, $options) && $withCache) {
            return $options[$option];
        }

        $retval = false;
        if (null !== $xoopsModuleConfig && (is_object($xoopsModule) && ($xoopsModule->getVar('dirname') == $repmodule) && $xoopsModule->getVar('isactive'))) {
            if (isset($xoopsModuleConfig[$option])) {
                $retval = $xoopsModuleConfig[$option];
            }
        } else {
            /** @var \XoopsModuleHandler $moduleHandler */
            $moduleHandler = xoops_getHandler('module');
            $module        = $moduleHandler->getByDirname($repmodule);
            $configHandler = xoops_getHandler('config');
            if ($module) {
                $moduleConfig = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
                if (isset($moduleConfig[$option])) {
                    $retval = $moduleConfig[$option];
                }
            }
        }
        $options[$option] = $retval;

        return $retval;
    }

    /**
     * Is Xoops 2.3.x ?
     *
     * @return boolean need to say it ?
     */
    //    function isX23()
    //    {
    //        $x23 = false;
    //        $xv  = str_replace('XOOPS ', '', XOOPS_VERSION);
    //        if ((int)(substr($xv, 2, 1)) >= 3) {
    //            $x23 = true;
    //        }
    //
    //        return $x23;
    //    }

    /**
     * Retreive an editor according to the module's option "form_options"
     *
     * @param string $caption Caption to give to the editor
     * @param string $name    Editor's name
     * @param string $value   Editor's value
     * @param string $width   Editor's width
     * @param string $height  Editor's height
     * @param string $supplemental
     *
     * @return \XoopsFormDhtmlTextArea|\XoopsFormEditor The editor to use
     */
    public static function getWysiwygForm(
        $caption,
        $name,
        $value = '',
        $width = '100%',
        $height = '400px',
        $supplemental = ''
    ) {
        global $xoopsModuleConfig;
        if (class_exists('XoopsFormEditor')) {
            $options['name']   = $name;
            $options['value']  = $value;
            $options['rows']   = 35;
            $options['cols']   = '100%';
            $options['width']  = '100%';
            $options['height'] = '400px';
            $editor            = new \XoopsFormEditor($caption, $xoopsModuleConfig['form_options'], $options, $nohtml = false, $onfailure = 'textarea');
        } else {
            $editor = new \XoopsFormDhtmlTextArea($caption, $name, $value, '100%', '100%');
        }

        return $editor;
    }

    /**
     * Create (in a link) a javascript confirmation's box
     *
     * @param string  $message Message to display
     * @param boolean $form    Is this a confirmation for a form ?
     *
     * @return string the javascript code to insert in the link (or in the form)
     */
    public static function javascriptLinkConfirm($message, $form = false)
    {
        if (!$form) {
            return "onclick=\"javascript:return confirm('" . str_replace("'", ' ', $message) . "')\"";
        } else {
            return "onSubmit=\"javascript:return confirm('" . str_replace("'", ' ', $message) . "')\"";
        }
    }

    /**
     * Redirect user with a message
     *
     * @param string $message message to display
     * @param string $url     The place where to go
     * @param        integer  timeout Time to wait before to redirect
     */
    public static function redirect($message = '', $url = 'index.php', $time = 2)
    {
        redirect_header($url, $time, $message);
    }

    /**
     * Internal function used to get the handler of the current module
     *
     * @return \XoopsModule The module
     */
    protected static function getModule()
    {
        static $mymodule;
        if (null === $mymodule) {
            global $xoopsModule;
            if (null !== $xoopsModule && is_object($xoopsModule) && REFERENCES_DIRNAME == $xoopsModule->getVar('dirname')) {
                $mymodule =& $xoopsModule;
            } else {
                $hModule  = xoops_getHandler('module');
                $mymodule = $hModule->getByDirname(REFERENCES_DIRNAME);
            }
        }

        return $mymodule;
    }

    /**
     * Returns the module's name (as defined by the user in the module manager) with cache
     *
     * @return string Module's name
     */
    public static function getModuleName()
    {
        static $moduleName;
        if (null === $moduleName) {
            $mymodule   = self::getModule();
            $moduleName = $mymodule->getVar('name');
        }

        return $moduleName;
    }

    /**
     * This function indicates if the current Xoops version needs to add asterisks to required fields in forms
     *
     * @return boolean Yes = we need to add them, false = no
     */
    public static function needsAsterisk()
    {
        if (self::isX23()) {
            return false;
        }
        if (false !== stripos(XOOPS_VERSION, 'impresscms')) {
            return false;
        }
        if (false === stripos(XOOPS_VERSION, 'legacy')) {
            $xv = xoops_trim(str_replace('XOOPS ', '', XOOPS_VERSION));
            if ((int)substr($xv, 4, 2) >= 17) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mark the mandatory fields of a form with a star
     *
     * @param \XoopsThemeForm $sform The form to modify
     *
     * @internal param string $caracter The character to use to mark fields
     * @return \XoopsThemeForm The modified form
     */
    public static function formMarkRequiredFields(&$sform)
    {
        $required = $elements = [];
        if (self::needsAsterisk()) {
            foreach ($sform->getRequired() as $item) {
                $required[] = $item->_name;
            }
            $elements =& $sform->getElements();
            $cnt      = count($elements);
            for ($i = 0; $i < $cnt; ++$i) {
                if (is_object($elements[$i]) && in_array($elements[$i]->_name, $required)) {
                    $elements[$i]->_caption .= ' *';
                }
            }
        }

        return $sform;
    }
}
