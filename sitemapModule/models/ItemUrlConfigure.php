<?php
/**
 * Created by PhpStorm.
 * User: maltsev
 * Date: 20.10.2017
 * Time: 15:42
 */

namespace webivan\sitemap\models;

use Yii;
use yii\base\Model;

class ItemUrlConfigure extends Model
{
    /**
     * @var string
     */
    public $loc;

    /**
     * @var string date('c')
     */
    public $lastmod;

    /**
     * @var string
     */
    public $changefreq;

    /**
     * @var string
     */
    public $priority;
    
    /**
     * @var bool
     */
    public $checkStatusUrl = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loc'], 'required'],
            [['loc', 'lastmod', 'changefreq', 'priority'], 'string'],
            ['lastmod', 'filter', 'filter' => function ($value) {
                if (($time = strtotime($value)) > 0) {
                    return date('c', $time);
                } else {
                    return $value;
                }
            }],
            [['checkStatusUrl'], 'boolean']
        ];
    }

    public function accessTags(): array
    {
        return [
            'loc' => $this->loc,
            'lastmod' => $this->lastmod,
            'changefreq' => $this->changefreq,
            'priority' => $this->priority
        ];
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->checkStatusUrl && !$this->isStatusOk()) {
            $this->addError('checkStatusUrl', 'Page status has been failed');
        }

        return true;
    }

    public function isStatusOk()
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $this->loc);
            $statusCode = $response->getStatusCode();

            return $statusCode >= 200 && $statusCode < 300;
        } catch (\Exception $e) {
            $this->addError('checkStatusUrl', $e->getMessage());
            return false;
        }
    }
}
