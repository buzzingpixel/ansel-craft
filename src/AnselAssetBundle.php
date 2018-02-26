<?php

namespace buzzingpixel\ansel;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * Class AnselAssetBundle
 */
class AnselAssetBundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@ansel/resources';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/script.min.js',
        ];

        $this->css = [
            'css/style.min.css',
        ];

        parent::init();
    }
}
