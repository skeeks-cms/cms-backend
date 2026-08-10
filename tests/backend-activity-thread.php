<?php

function backendActivityThreadExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$modelLogView = file_get_contents(dirname(__DIR__).'/src/actions/views/model-log.php');

backendActivityThreadExpect(
    strpos($modelLogView, "'options' => ['class' => 'sx-activity-thread']") !== false,
    'Generic model activity does not use the shared activity thread layout.'
);
backendActivityThreadExpect(
    strpos($modelLogView, "'title'    => 'Добавить комментарий'") !== false,
    'Generic model activity composer has no visible heading.'
);
backendActivityThreadExpect(
    strpos($modelLogView, 'sx-model-log-comment') !== false,
    'Generic model activity lost its composer hook.'
);

echo "Backend activity thread contract: OK\n";
