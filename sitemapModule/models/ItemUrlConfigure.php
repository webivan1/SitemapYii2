<?php
/**
 * Created by PhpStorm.
 * User: maltsev
 * Date: 20.10.2017
 * Time: 15:42
 */

namespace common\modules\sitemap\models;

use Yii;
use yii\base\Model;

class ItemUrlConfigure extends Model
{
    public $loc;
    public $lastmod;
    public $changefreq;
    public $priority;

    public function rules()
    {
        return [
            [['loc'], 'required'],
            [['loc', 'lastmod', 'changefreq', 'priority'], 'string'],
            [['lastmod'], 'date']
        ];
    }
}