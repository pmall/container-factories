<?php

return [
    'parameters' => [
        'id1' => 'parameter1',
        'id2' => 'parameter2',
    ],
    'aliases' => [
        'id2' => 'alias1',
        'id3' => 'alias2',
    ],
    'invokables' => [
        'id3' => Test\TestInvokable::class,
        'id4' => Test\TestInvokable::class,
    ],
    'factories' => [
        'id4' => new Test\TestFactory('factory1'),
        'id5' => new Test\TestFactory('factory2'),
    ],
    'tags' => [
        'id5' => ['tag11', 'key' => 'tag12', 'tag13'],
        'id6' => ['tag21', 'key' => 'tag22', 'tag23'],
    ],
    'mappers' => [
        'id6' => Test\SomeInterface1::class,
        'id7' => Test\SomeInterface2::class,
    ],
    'extensions' => [
        'id7' => new Test\TestFactory('extension1'),
        'id8' => new Test\TestFactory('extension2'),
    ],
    'passes' => [
        new Test\TestProcessingPass('pass1'),
        'key' => new Test\TestProcessingPass('pass2'),
        new Test\TestProcessingPass('pass3'),
    ],
    'key' => 'only here to ensure extra keys are ignored without errors',
];
