<?php namespace XoopsModules\Marquee;

use \XoopsModules\Marquee\Common;

/**
 * Class Utility
 */
class Utility
{
    use Common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use Common\ServerStats; // getServerStats Trait

    use Common\FilesManagement; // Files Management Trait

    //--------------- Custom module methods -----------------------------
    const MODULE_NAME = 'marquee';

    /**
     * truncateHtml can truncate a string up to a number of characters while preserving whole words and HTML tags
     * www.gsdesign.ro/blog/cut-html-string-without-breaking-the-tags
     * www.cakephp.org
     *
     * @param string  $text         String to truncate.
     * @param integer $length       Length of returned string, including ellipsis.
     * @param string  $ending       Ending to be appended to the trimmed string.
     * @param boolean $exact        If false, $text will not be cut mid-word
     * @param boolean $considerHtml If true, HTML tags would be handled correctly
     *
     * @return string Trimmed string.
     */
    public static function truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true)
    {
        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?' . '>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?' . '>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags    = [];
            $truncate     = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                        // if tag is a closing tag
                    } elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if (false !== $pos) {
                            unset($open_tags[$pos]);
                        }
                        // if tag is an opening tag
                    } elseif (preg_match('/^<\s*([^\s>!]+).*?' . '>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length + $content_length > $length) {
                    // the number of characters which are left
                    $left            = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate     .= $line_matchings[2];
                    $total_length += $content_length;
                }
                // if the maximum length is reached, get off the loop
                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = substr($text, 0, $length - strlen($ending));
            }
        }
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        if ($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

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
//    public static function getModuleOption($option, $withCache = true)
//    {
//        global $xoopsModuleConfig, $xoopsModule;
//        $repmodule = self::MODULE_NAME;
//        static $options = [];
//        if (is_array($options) && array_key_exists($option, $options) && $withCache) {
//            return $options[$option];
//        }
//
//        $retval = false;
//        if (null !== $xoopsModuleConfig && (is_object($xoopsModule) && ($xoopsModule->getVar('dirname') == $repmodule) && $xoopsModule->getVar('isactive'))) {
//            if (isset($xoopsModuleConfig[$option])) {
//                $retval = $xoopsModuleConfig[$option];
//            }
//        } else {
//            /** @var \XoopsModuleHandler $moduleHandler */
//            $moduleHandler = xoops_getHandler('module');
//            $module        = $moduleHandler->getByDirname($repmodule);
//            $configHandler = xoops_getHandler('config');
//            if ($module) {
//                $moduleConfig = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
//                if (isset($moduleConfig[$option])) {
//                    $retval = $moduleConfig[$option];
//                }
//            }
//        }
//        $options[$option] = $retval;
//
//        return $retval;
//    }

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
                $mymodule = $xoopsModule;
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

    /**
     * @param        $option
     * @param string $repmodule
     * @return bool
     */
    public static function getModuleOption($option, $repmodule = 'marquee')
    {
        global $xoopsModuleConfig, $xoopsModule;
        static $tbloptions = [];
        if (is_array($tbloptions) && array_key_exists($option, $tbloptions)) {
            return $tbloptions[$option];
        }

        $retval = false;
        if (null !== $xoopsModuleConfig
            && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule
                && $xoopsModule->getVar('isactive'))) {
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
        $tbloptions[$option] = $retval;

        return $retval;
    }

    /**
     * Verify if the current "user" is a bot or not
     *
     * If you have a problem with this function, insert the folowing code just before the line if (isset($_SESSION['news_cache_bot'])) { :
     * return false;
     *
     * @package          Marquee
     * @author           Hervé Thouzard (http://www.herve-thouzard.com)
     * @copyright    (c) Hervé Thouzard
     */
    public static function isBot()
    {
        if (isset($_SESSION['marquee_cache_bot'])) {
            return $_SESSION['marquee_cache_bot'];
        } else {
            // Add here every bot you know separated by a pipe | (not matter with the upper or lower cases)
            // If you want to see the result for yourself, add your navigator's user agent at the end (mozilla for example)
            $botlist      = 'AbachoBOT|Arachnoidea|ASPSeek|Atomz|cosmos|crawl25-public.alexa.com|CrawlerBoy Pinpoint.com|Crawler|DeepIndex|EchO!|exabot|Excalibur Internet Spider|FAST-WebCrawler|Fluffy the spider|GAIS Robot/1.0B2|GaisLab data gatherer|Google|Googlebot-Image|googlebot|Gulliver|ia_archiver|Infoseek|Links2Go|Lycos_Spider_(modspider)|Lycos_Spider_(T-Rex)|MantraAgent|Mata Hari|Mercator|MicrosoftPrototypeCrawler|Mozilla@somewhere.com|MSNBOT|NEC Research Agent|NetMechanic|Nokia-WAPToolkit|nttdirectory_robot|Openfind|Oracle Ultra Search|PicoSearch|Pompos|Scooter|Slider_Search_v1-de|Slurp|Slurp.so|SlySearch|Spider|Spinne|SurferF3|Surfnomore Spider|suzuran|teomaagent1|TurnitinBot|Ultraseek|VoilaBot|vspider|W3C_Validator|Web Link Validator|WebTrends|WebZIP|whatUseek_winona|WISEbot|Xenu Link Sleuth|ZyBorg';
            $botlist      = strtoupper($botlist);
            $currentagent = strtoupper(xoops_getenv('HTTP_USER_AGENT'));
            $retval       = false;
            $botarray     = explode('|', $botlist);
            foreach ($botarray as $onebot) {
                if (false !== strpos($currentagent, $onebot)) {
                    $retval = true;
                    break;
                }
            }
        }
        $_SESSION['marquee_cache_bot'] = $retval;

        return $retval;
    }

    /**
     * Escape a string so that it can be included in a javascript string
     *
     * @param $string
     *
     * @return mixed
     */
    public static function javascriptEscape($string)
    {
        return str_replace("'", "\\'", $string);
    }
}
