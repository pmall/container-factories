<?php

return [
    'mappers' => [
        'id1' => new Test\TestProcessingPass('passes1'),
        'id2' => 1,
        'id3' => new Test\TestProcessingPass('passes1'),
    ],
];
