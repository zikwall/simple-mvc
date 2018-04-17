<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 30.01.2018
 * Time: 16:39
 */

namespace core\web;


class View extends \core\base\View
{
    /**
     * @var AssetBundle[] list of the registered asset bundles. The keys are the bundle names, and the values
     * are the registered [[AssetBundle]] objects.
     * @see registerAssetBundle()
     */
    public $assetBundles = [];
    /**
     * @var string the page title
     */
    public $title;
    /**
     * @var array the registered meta tags.
     * @see registerMetaTag()
     */
    public $metaTags = [];
    /**
     * @var array the registered link tags.
     * @see registerLinkTag()
     */
    public $linkTags = [];
    /**
     * @var array the registered CSS code blocks.
     * @see registerCss()
     */
    public $css = [];
    /**
     * @var array the registered CSS files.
     * @see registerCssFile()
     */
    public $cssFiles = [];
    /**
     * @var array the registered JS code blocks
     * @see registerJs()
     */
    public $js = [];
    /**
     * @var array the registered JS files.
     * @see registerJsFile()
     */
    public $jsFiles = [];

    private $_assetManager;

}
