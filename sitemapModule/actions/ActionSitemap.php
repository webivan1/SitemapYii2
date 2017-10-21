<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21.10.2017
 * Time: 16:27
 */

namespace webivan\sitemap\actions;

use Yii;
use yii\base\Action;

class ActionSitemap extends Action
{
    /**
     * Generate sitemaps action
     *
     * @return void
     */
    public function run()
    {
        Yii::$app->sitemapComponent->createSitemaps();
    }
}