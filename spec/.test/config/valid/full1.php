<?php

return [
    'parameters' => [
        'id1' => 'parameter11',
        'id2' => 'parameter12',
    ],
    'aliases' => [
        'id2' => 'alias11',
        'id3' => 'alias12',
    ],
    'factories' => [
        'id3' => new Test\TestFactory('factory11'),
        'id4' => new Test\TestFactory('factory12'),
    ],
    'extensions' => [
        'id4' => new Test\TestFactory('extension11'),
        'id5' => new Test\TestFactory('extension12'),
    ],
    'tags' => [
        'id5' => ['tag111','tag112'],
        'id6' => ['tag121','tag122'],
    ],
    'metadata' => [
        'id1' => ['k111' => 'm111', 'k112' => 'm112'],
        'id2' => ['k121' => 'm111', 'k122' => 'm122'],
    ],
    'passes' => [
        'key' => new Test\TestConfigurationPass('pass11'),
        new Test\TestConfigurationPass('pass12'),
    ],
    'mappers' => [
        Test\SomeInterface1::class => 'mapper11',
        Test\SomeInterface2::class => 'mapper12',
    ],
    'key' => 'only here to ensure extra keys are ignored without errors',
];
