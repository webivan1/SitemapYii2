<?php
/**
 * Created by PhpStorm.
 * User: maltsev
 * Date: 20.10.2017
 * Time: 12:00
 */

namespace common\modules\sitemap\models;

use Yii;
use yii\helpers\Html;

class GenerateUrls
{
    private $models;

    public function setModels(array $models)
    {
        $this->models = $models;
    }

    public function run()
    {
        foreach ($this->models as $item)
        {
            if (is_array($item)) {
                $container = new ItemConfigure();
                $container->setAttributes($item);

                if ($container->validate()) {
                    $datas = $container->getDatas();

                    if ($datas->valid()) {
                        foreach ($datas as $data) {
                            yield $data;
                        }
                    }
                } else {
                    // @toDo пофиксить
                    Yii::error(Html::errorSummary($container), 'sitemap');
                    continue;
                }

            } else if (is_callable($item)) {
                $urlData = call_user_func($item);

                if (!empty($urlData)) {
                    yield ItemConfigure::createObjectUrls($urlData);
                }
            }
        }
    }
}