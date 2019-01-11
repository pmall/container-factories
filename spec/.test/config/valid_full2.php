<?php

return [
    'parameters' => [
        'id1' => 'parameter21',
        'id2' => 'parameter22',
    ],
    'aliases' => [
        'id2' => 'alias21',
        'id3' => 'alias22',
    ],
    'factories' => [
        'id3' => new Test\TestFactory('factory21'),
        'id4' => new Test\TestFactory('factory22'),
    ],
    'extensions' => [
        'id4' => new Test\TestFactory('extension21'),
        'id5' => new Test\TestFactory('extension22'),
    ],
    'tags' => [
        'id5' => ['alias211', 'alias212'],
        'id6' => ['alias221', 'alias222'],
    ],
    'another_key' => [
        'id7' => 'only here to ensure extra keys are ignored without errors',
    ],
];
