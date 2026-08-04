SkeekS CMS backend
===================================

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist skeeks/cms-backend "*"
```

or add

```
"skeeks/cms-backend": "*"
```

Configuration app
----------


```php
[
    'bootstrap'     => ['backendAdmin'],
    'components'    =>
    [
        'backendAdmin' =>
        [
            'id'                => 'id',
            'class'             => 'skeeks\cms\backend\BackendComponent',
            'controllerPrefix'  => 'admin',
            'urlRule'           => [
                'urlPrefix' => '~admin'
            ],
            'allowedIPs' => [
                '91.*',
                '92.219.167.252',
            ]
        ],
    ],
]
```

Semantic palette
----------------

`BackendTheme::$palette` is the opt-in storage contract for project and future
user-editable themes. It accepts partial `light` and `dark` maps with only ten
base colors: `accent`, `canvas`, `surface`, `surfaceMuted`, `text`, `textMuted`,
`border`, `success`, `warning`, and `danger`.

```php
'view' => [
    'theme' => [
        'class' => \skeeks\cms\backend\themes\BackendTheme::class,
        'palette' => [
            'light' => ['accent' => '#1769aa'],
            'dark' => ['accent' => '#73b7f2'],
        ],
    ],
],
```

The backend validates hex colors and expands them into the full public
`--sx-color-*` facade, including state, soft, contrast, and focus colors.
Component tokens remain internal. Brand gradients and product-specific layout
geometry stay in project CSS; arbitrary CSS variables are not accepted here.

Links
-----
* [Web site](https://cms.skeeks.com)
* [Author](https://skeeks.com)
* [ChangeLog](https://github.com/skeeks-cms/cms-backend/blob/master/CHANGELOG.md)

___

> [![skeeks!](https://skeeks.com/img/logo/logo-no-title-80px.png)](https://skeeks.com)  
<i>SkeekS CMS (Yii2) — fast, simple, effective!</i>  
[skeeks.com](https://skeeks.com) | [cms.skeeks.com](https://cms.skeeks.com)


