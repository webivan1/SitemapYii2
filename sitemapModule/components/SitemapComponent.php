<?php
/**
 * Created by PhpStorm.
 * User: maltsev
 * Date: 20.10.2017
 * Time: 11:28
 */

namespace webivan\sitemap;

use Yii;
use yii\base\Component;
use webivan\sitemap\models\GenerateUrls;
use webivan\sitemap\models\ItemConfigure;
use webivan\sitemap\models\XmlGenerate;

class SitemapComponent extends Component
{
    /**
     * Path save sitemap files
     *
     * @property string
     */
    public $pathSitemapFiles = '@webroot/sitemaps';

    /**
     * Cache duration
     *
     * @property int
     */
    public $timeLive = 3600 * 24 * 5; // 5 days

    /**
     * Max records sitemap file
     *
     * @property string
     */
    public $maxMapRecords = 50000;

    /**
     * @property string
     */
    public $runtimePath = '@app/runtime';

    /**
     * @property array
     */
    public $models;

    /**
     * @property array
     */
    public $staticUrl;

    /**
     * @property string
     */
    public $xmlns = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    /**
     * @property string
     */
    public $domain = '';

    /**
     * @property string
     */
    public $baseNameFile = 'sitemap';

    /**
     * @property string
     */
    public $defaultPriority = '0.7';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (strpos($this->pathSitemapFiles, '@') === 0) {
            $this->pathSitemapFiles = Yii::getAlias($this->pathSitemapFiles);
        }
    }

    /**
     * Is cache
     *
     * @return bool
     */
    public function isTimeout()
    {
        $cacheComponent = Yii::$app->getCache();
        $keyCache = 'SitemapKeyCache';

        if ($cacheComponent) {
            if ($cacheComponent->exists($keyCache)) {
                return false;
            } else {
                $cacheComponent->set($keyCache, 'ok', $this->timeLive);
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * Create sitemap files
     *
     * @return void
     */
    public function createSitemaps()
    {
        $xmlModel = new XmlGenerate();

        // default url
        $xmlModel->appendTo(
            ItemConfigure::createObjectUrls($this->staticUrl)
        );

        if (!empty($this->models)) {
            $models = new GenerateUrls();
            $models->setModels($this->models);

            $result = $models->run();

            if ($result->valid()) {
                foreach ($result as $urls) {
                    $xmlModel->appendTo($urls);
                }
            }
        }

        $xmlModel->createFile();
    }

    /**
     * Main template sitemap.xml
     *
     * @return string
     */
    public function renderWrapper()
    {
        return (new XmlGenerate(false))->wrapper();
    }
}