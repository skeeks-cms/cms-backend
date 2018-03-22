<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 08.03.2017
 */
return [
    'components' => [
        'i18n' => [
            'translations' => [
                'skeeks/backend' => [
                    'class'    => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@skeeks/cms/backend/messages',
                    'fileMap'  => [
                        'skeeks/backend' => 'main.php',
                    ],
                ],
            ],
        ],
    ],

    'modules' => [
        'backend' => [
            'class' => '\skeeks\cms\backend\BackendModule',
        ],
    ],
];