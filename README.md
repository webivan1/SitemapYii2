# Ext Sitemap Yii 2

Требования
----------

Class PHP: `XMLWriter`

Установка
---------

```
composer require yii2-webivan1/yii2-sitemap
```
Или 
```
"require": {
    "yii2-webivan1/yii2-sitemap": "dev-master"
}
```
 
Настройка
---------
 
Конфиг `web.php`

```php

<?php

return [
    // ...
    
    'bootstrap' => [
        
        // ...
        
        'sitemap'
    ],
    
    'modules' => [
        
        // ...
        
        'sitemap' => [
            'class' => 'webivan\sitemap\SitemapModule',
            
            // get action /sitemap.xml
            'defaultSitemapUrl' => 'sitemap.xml',
            
            // Куда сохраняем логи
            'runtimePath' => '@app/runtime/logs/sitemap.log'
        ]
    ],
    
    'components' => [
        'sitemapComponent' => [
            'class' => 'webivan\sitemap\components\SitemapComponent',
            
            // Отключаем генерацию sitemap при
            // открытии ссылки /sitemap.xml
            'generateSitemapsByUrl' => false,
            
            // Если у нас генерируются файлы через урл,
            // то кэшируем их
            'timeLive' => 3600 * 24 * 5,
            
            // Ключ для кэширования
            'cacheNameKey' => 'SitemapKeyCache',
            
            // Приоритет страниц подефолту,
            // можно убрать поставив значение null
            'defaultPriority' => '0.7',
            
            // Домен который будет в sitemap.xml
            // например http://moovie.pro
            'domain' => 'http://moovie.pro',
            
            // Указывая путь для sitemap файлов,
            // создайте предварительно все папки с правами
            // для записи и чтения
            'pathSitemapFiles' => '@webroot/sitemaps',
            
            // Статические урлы
            'staticUrl' => [
                ['loc' => '/', 'priority' => '1'],
                ['loc' => '/about'],
            ],
            
            // Конфигурация
            'models' => [
                // Вы можете описать функицю которая будет возвращать
                // список урлов
                function (): array {
                    $models = \common\models\Pages::findAll(['state' => 2]);
                    $output = [];

                    foreach ($models as $model) {
                        $output[] = [
                            'loc' => "/{$model->alias}",
                            'changefreq' => 'daily'
                        ];
                    }

                    return $output;
                },
                // Вы можете указать конфиг параметров для авто генерации урлов
                // Если данных много то рекомендую использовать этот способ
                [
                    'model' => 'common\models\Product',
                    'select' => 'id, alias',
                    'where' => 'state = 2',
                    'urls' => [
                        ['loc' => '/products/{alias}', 'changefreq' => 'daily'],
                        ['loc' => '/product/detail/{id}', 'changefreq' => 'daily'],
                    ],
                    // динамические урлы
                    'appendUrls' => function () {
                        $tags = Yii::$app->params['tags'];
    
                        return array_map(function ($append) {
                            return ['loc' => "/tags/{$append}/{alias}"];
                        }, $tags);
                    }
                ],
                
                // ...
            ]
        ]
    ]

];

```

Генерация файлов через консоль
------------------------------

`В консольном конфиге определите алиас @webroot
если используете путь подефолту`

Например:

```php 
<?php

return [
    'aliases' => [
        '@webroot' => realpath(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'www')
    ],
];
```

Создайте контроллер для консольных комманд, например

```php 
<?php 

namespace app\commands;

use yii\console\Controller;

class SitemapController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'webivan\sitemap\actions\ActionSitemap'
            ]
        ];
    }
}
```

и запускайте: `php yii sitemap/index`