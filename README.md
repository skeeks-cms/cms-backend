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

```
"repositories": [
    {
        "type": "git",
        "url":  "https://github.com/skeeks-cms/cms-backend.git"
    }
]
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

Links
-----
* [Web site (rus)](https://cms.skeeks.com)
* [Author](https://skeeks.com)
* [ChangeLog](https://github.com/skeeks-cms/cms-vk-database/blob/master/CHANGELOG.md)

___

> [![skeeks!](https://skeeks.com/img/logo/logo-no-title-80px.png)](https://skeeks.com)  
<i>SkeekS CMS (Yii2) — fast, simple, effective!</i>  
[skeeks.com](https://skeeks.com) | [cms.skeeks.com](https://cms.skeeks.com)


