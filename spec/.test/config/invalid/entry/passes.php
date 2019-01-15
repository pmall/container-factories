<?php

return [
    'parameters' => [],
    'aliases' => [],
    'factories' => [],
    'extensions' => [],
    'tags' => [],
    'metadata' => [],
    'passes' => [
        'key' => new Test\TestConfigurationPass('pass1'),
        1,
        new Test\TestConfigurationPass('pass3'),
    ],
    'mappers' => [],
];
