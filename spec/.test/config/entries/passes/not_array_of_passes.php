<?php

return [
    'passes' => [
        new Test\TestProcessingPass('pass1'),
        new class {},
        new Test\TestProcessingPass('pass3'),
    ],
];
