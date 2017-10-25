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
     * @property string
     */
    public $loc;

    /**
     * @property string date('c')
     */
    public $lastmod;

    /**
     * @property string
     */
    public $changefreq;

    /**
     * @property string
     */
    public $priority;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loc'], 'required'],
            [['loc', 'lastmod', 'changefreq', 'priority'], 'string'],
            ['lastmod', 'filter', 'filter' => function ($value) {
                return date('c', strtotime($value));
            }],
            ['lastmod', 'date']
        ];
    }
}
