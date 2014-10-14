<?php

/**
 * NicheCommerce
 *
 * @category    NicheCommerce
 * @package     NicheCommerce
 * @copyright   Copyright (c) 2012 NicheCommerce. (http://nichecommerce.nl)
 * @author      Tim Vroom (tim@nichecommerce.nl)
 */
class Timvroom_Custompdf_Helper_Dompdf extends Mage_Core_Helper_Abstract
{
    const XML_PATH_GENERAL_PAPER_SIZE = 'custompdf/general/paper_size';
    const XML_PATH_GENERAL_FONT = 'custompdf/general/font';
    const XML_PATH_GENERAL_HTML5 = 'custompdf/general/enable_html5';

    const XML_PATH_DEVELOPMENT_LOG_FILE_NAME = 'custompdf/development/log_file_name';
    const XML_PATH_DEVELOPMENT_SHOW_WARNINGS = 'custompdf/development/show_warnings';
    const XML_PATH_DEVELOPMENT_DEBUG = 'custompdf/development/debug';
    const XML_PATH_DEVELOPMENT_DEBUG_CSS = 'custompdf/development/debug_css';
    const XML_PATH_DEVELOPMENT_DEBUG_KEEP_TEMP = 'custompdf/development/debug_keep_temp';
    const XML_PATH_DEVELOPMENT_DEUBG_LAYOUT = 'custompdf/development/debug_layout';

    /** @var bool $_isDompdfIncluded */
    static private $_isDompdfIncluded = false;

    /**
     * Initialize
     */
    public function __construct()
    {
        $this->setupDompdf();
    }

    /**
     * Setup the dompdf environment
     *
     * @todo validate the dompdf path and allow custom location
     */
    public function setupDompdf()
    {
        if (!static::$_isDompdfIncluded) {
            define("DOMPDF_DIR", dirname(Mage::getBaseDir()) . DS . 'vendor' . DS . 'dompdf' . DS . 'dompdf');
            define("DOMPDF_INC_DIR", DOMPDF_DIR . "/include");
            define("DOMPDF_LIB_DIR", DOMPDF_DIR . "/lib");
            require_once(DOMPDF_INC_DIR . "/functions.inc.php");

            def("DOMPDF_FONT_DIR", DOMPDF_DIR . "/lib/fonts/");
            def("DOMPDF_FONT_CACHE", Mage::getBaseDir('cache') . DS . implode(DS, array('dompdf', 'fonts')));
            if (!is_dir(DOMPDF_FONT_CACHE)) {
                mkdir(DOMPDF_FONT_CACHE, 0777, true);
            }
            def("DOMPDF_TEMP_DIR", Mage::getBaseDir('tmp') . DS . implode(DS, array('dompdf', 'temp')));
            def("DOMPDF_CHROOT", realpath(DOMPDF_DIR));
            def("DOMPDF_UNICODE_ENABLED", true);
            def("DOMPDF_ENABLE_FONTSUBSETTING", false);
            def("DOMPDF_PDF_BACKEND", "CPDF");
            def("DOMPDF_DEFAULT_MEDIA_TYPE", "screen");
            def("DOMPDF_DEFAULT_PAPER_SIZE", $this->getDefaultPaperSize());
            def("DOMPDF_DEFAULT_FONT", $this->getDefaultFont());
            def("DOMPDF_DPI", 96);
            def("DOMPDF_ENABLE_PHP", false);
            def("DOMPDF_ENABLE_JAVASCRIPT", true);
            def("DOMPDF_ENABLE_REMOTE", false);
            def("DOMPDF_LOG_OUTPUT_FILE", Mage::getBaseDir('log') . DS . $this->getLogFile());
            def("DOMPDF_FONT_HEIGHT_RATIO", 1.1);
            def("DOMPDF_ENABLE_CSS_FLOAT", false);
            def("DOMPDF_AUTOLOAD_PREPEND", false);
            def("DOMPDF_ENABLE_HTML5PARSER", $this->isEnabledHtml5());
            require_once(DOMPDF_LIB_DIR . "/html5lib/Parser.php");
            require_once(DOMPDF_INC_DIR . "/autoload.inc.php");
            require_once(DOMPDF_LIB_DIR . "/php-font-lib/classes/Font.php");

            mb_internal_encoding('UTF-8');

            global $_dompdf_warnings;
            $_dompdf_warnings = array();
            global $_dompdf_show_warnings;
            $_dompdf_show_warnings = false;
            global $_dompdf_debug;
            $_dompdf_debug       = $this->isDebugPdf();
            $_DOMPDF_DEBUG_TYPES = array();
            def('DEBUGPNG', false);
            def('DEBUGKEEPTEMP', $this->isDebugKeepTemp());
            def('DEBUGCSS', $this->isDebugCss());
            def('DEBUG_LAYOUT', $this->isDebugLayout());
            def('DEBUG_LAYOUT_LINES', true);
            def('DEBUG_LAYOUT_BLOCKS', true);
            def('DEBUG_LAYOUT_INLINE', true);
            def('DEBUG_LAYOUT_PADDINGBOX', true);
            static::$_isDompdfIncluded = true;
        }
    }

    public function getDomPdf()
    {
        return new DOMPDF();
    }

    public function getPaperSizes()
    {
        return array_keys(CPDF_Adapter::$PAPER_SIZES);
    }

    public function getFontFamilies(){
        $fonts = include DOMPDF_FONT_DIR . DS .'dompdf_font_family_cache.dist.php';
        return array_keys($fonts);
    }

    public function getDefaultPaperSize($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_PAPER_SIZE, $storeId) ?: "a4";
    }

    public function getDefaultFont($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_FONT, $storeId) ?: "serif";
    }

    public function getLogFile($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_DEVELOPMENT_LOG_FILE_NAME, $storeId) ?: "pdf.log";
    }

    public function isEnabledHtml5($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_GENERAL_HTML5, $storeId);
    }

    public function isDebugPdf($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DEVELOPMENT_DEBUG, $storeId);
    }

    public function isDebugKeepTemp($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DEVELOPMENT_DEBUG_KEEP_TEMP, $storeId);
    }

    public function isDebugCss($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DEVELOPMENT_DEBUG_CSS, $storeId);
    }

    public function isDebugLayout($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DEVELOPMENT_DEUBG_LAYOUT, $storeId);
    }
}