<?php

return [
    'parameters' => [
        'id01' => 'parameter1',
        'id02' => 'parameter2',
    ],
    'aliases' => [
        'id02' => 'alias1',
        'id03' => 'alias2',
    ],
    'invokables' => [
        'id03' => Test\TestInvokable::class,
        'id04' => Test\TestInvokable::class,
    ],
    'factories' => [
        'id04' => new Test\TestFactory('factory1'),
        'id05' => new Test\TestFactory('factory2'),
    ],
    'tags' => [
        'id05' => ['tag11', 'key' => 'tag12', 'tag13'],
        'id06' => ['tag21', 'key' => 'tag22', 'tag23'],
    ],
    'mappers' => [
        'id07' => Test\SomeInterface1::class,
        'id08' => Test\SomeInterface2::class,
    ],
    'extensions' => [
        'id09' => new Test\TestFactory('extension1'),
        'id10' => new Test\TestFactory('extension2'),
    ],
    'passes' => [
        new Test\TestProcessingPass('pass1'),
        'key' => new Test\TestProcessingPass('pass2'),
        new Test\TestProcessingPass('pass3'),
    ],
    'key' => 'only here to ensure extra keys are ignored without errors',
];
