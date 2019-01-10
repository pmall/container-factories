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
    'another_key' => [
        'id5' => 'only here to ensure extra keys are ignored without errors',
    ],
];
