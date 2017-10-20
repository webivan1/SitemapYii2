<?php
/**
 * Created by PhpStorm.
 * User: maltsev
 * Date: 20.10.2017
 * Time: 11:24
 */

namespace webivan\sitemap;

use Yii;
use yii\base\Module;
use yii\base\BootstrapInterface;

class SitemapModule extends Module implements BootstrapInterface
{
    /**
     * @property string
     */
    public $defaultSitemapUrl = 'sitemap.xml';

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules([
            [
                'class' => 'yii\web\UrlRule',
                'pattern' => $this->defaultSitemapUrl,
                'route' => $this->id . '/default/index'
            ],
        ], false);

        array_push($app->getLog()->targets, [
            'class' => 'yii\log\FileTarget',
            'logFile' => '@app/runtime/logs/sitemap.log',
            'categories' => ['sitemap'],
            'logVars' => ['error', 'warning'],
        ]);
    }
}