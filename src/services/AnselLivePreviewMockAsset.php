<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\services;

use craft\helpers\Html;
use craft\elements\Asset;
use craft\helpers\Template;

/**
 * Class AnselLivePreviewMockAsset
 */
class AnselLivePreviewMockAsset extends Asset
{
    /** @var string $base64Image */
    public $base64Image;

    /**
     * Returns an `<img>` tag based on this asset.
     * @return \Twig_Markup|null
     */
    public function getImg()
    {
        $img = '<img src="'.$this->getUrl().'" alt="'.Html::encode($this->title).'">';
        return Template::raw($img);
    }

    /**
     * Returns the element’s full URL.
     * @param string|array|null $transform
     * @return string|null
     */
    public function getUrl($transform = null)
    {
        return $this->base64Image;
    }
}
