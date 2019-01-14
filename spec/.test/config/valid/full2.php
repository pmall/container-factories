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
        'id5' => ['tag211','tag212'],
        'id6' => ['tag221','tag222'],
    ],
    'metadata' => [
        'id1' => ['k211' => 'm211', 'k212' => 'm212'],
        'id2' => ['k221' => 'm211', 'k222' => 'm222'],
    ],
    'key' => 'only here to ensure extra keys are ignored without errors',
];
