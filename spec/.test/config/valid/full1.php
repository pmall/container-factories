<?php

return [
    'parameters' => [
        'id1' => 'parameter11',
        'id2' => 'parameter12',
    ],
    'aliases' => [
        'alias1' => 'id11',
        'alias2' => 'id12',
    ],
    'factories' => [
        'id2' => new Test\TestFactory('factory11'),
        'id3' => new Test\TestFactory('factory12'),
    ],
    'extensions' => [
        'id3' => new Test\TestFactory('extension11'),
        'id4' => new Test\TestFactory('extension12'),
    ],
    'tags' => [
        'tag1' => [
            'id111' => [],
            'id112' => [],
        ],
        'tag2' => [
            'id121' => [],
            'id122' => [],
        ],
    ],
    'key' => 'only here to ensure extra keys are ignored without errors',
];
