# Ext Sitemap Yii 2

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
            'class' => 'common\modules\sitemap\SitemapModule'
        ]
    ],
    
    'components' => [
        'sitemapComponent' => [
            'class' => 'common\modules\sitemap\components\SitemapComponent',
            'domain' => 'https://www.novostroy-m.ru',
            'staticUrl' => [
                ['loc' => '/', 'priority' => '1'],
                ['loc' => '/zakonjy'],
                ['loc' => '/o_proekte'],
                ['loc' => '/kontaktjy'],
                ['loc' => '/reklama'],
                ['loc' => '/moskva'],
                ['loc' => '/vse_spetspredlogenia'],
                ['loc' => '/spetspredlogenia_v_moskve'],
                ['loc' => '/vse_novostroyki_moskvy_i'],
                ['loc' => '/skidki_na_kvartiry'],
                ['loc' => '/deshevye_novostroyki_moskvy'],
                ['loc' => '/novostrojki_v_podmoskove'],
                ['loc' => '/po_rajonam'],
                ['loc' => '/po_gorodam'],
                ['loc' => '/po_metro'],
                ['loc' => '/gosipoteka'],
            ],
            'models' => [
                function (): array {
                    $models = \common\models\Novos::findAll(['state' => 2]);
                    $output = [];

                    foreach ($models as $model) {
                        $output[] = [
                            'loc' => "/baza/{$model->alias}",
                            'changefreq' => 'daily'
                        ];
                    }

                    return $output;
                },
                [
                    'model' => 'common\models\Novos',
                    'select' => 'alias',
                    'where' => 'state = 2',
                    'urls' => [
                        ['loc' => '/baza/{alias}', 'changefreq' => 'daily'],
                        ['loc' => '/kvartiry/novostroyka/{alias}', 'changefreq' => 'daily'],
                        ['loc' => '/baza/{alias}/planirovki', 'changefreq' => 'daily'],
                        ['loc' => '/baza/{alias}/ipoteka', 'changefreq' => 'daily'],
                        ['loc' => '/carparking/novostroyka/{alias}', 'changefreq' => 'daily'],
                        ['loc' => '/baza/{alias}/akcii', 'changefreq' => 'daily'],
                        ['loc' => '/baza/{alias}/infrastruktura', 'changefreq' => 'daily'],
                        ['loc' => '/baza/{alias}/otzyvy', 'changefreq' => 'daily'],
                    ],
                    'appendUrls' => function () {
                        return array_map(function ($append) {
                            return ['loc' => '/kvartiry/novostroyka/{alias}/' . $append];
                        }, array_keys(Yii::$app->params['aliasesUrlParam']));
                    }
                ],
                [
                    'model' => 'common\models\Ads',
                    'select' => 'alias',
                    'where' => 'state = 2',
                    'urls' => [
                        ['loc' => '/kvartiry/{alias}', 'changefreq' => 'daily'],
                    ]
                ]
            ]
        ]
    ]

];

```