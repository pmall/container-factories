<?php

return [
    'parameters' => [
        'id1' => 'parameter21',
        'id2' => 'parameter22',
    ],
    'aliases' => [
        'alias1' => 'id21',
        'alias2' => 'id22',
    ],
    'factories' => [
        'id2' => new Test\TestFactory('factory21'),
        'id3' => new Test\TestFactory('factory22'),
    ],
    'extensions' => [
        'id3' => new Test\TestFactory('extension21'),
        'id4' => new Test\TestFactory('extension22'),
    ],
    'tags' => [
        'tag1' => [
            'id211' => [],
            'id212' => [],
        ],
        'tag2' => [
            'id221' => [],
            'id222' => [],
        ],
    ],
    'key' => 'only here to ensure extra keys are ignored without errors',
];
