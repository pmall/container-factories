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
        'id6' => [
            'alias111' => [],
            'alias112' => [],
        ],
        'id7' => [
            'alias121' => [],
            'alias122' => [],
        ],
    ],
    'another_key' => [
        'id8' => 'only here to ensure extra keys are ignored without errors',
    ],
];
