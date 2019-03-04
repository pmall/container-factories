<?php

use Quanta\Container\Helpers\ArrayStr;

describe('ArrayStr', function () {

    context('when the array is empty', function () {

        describe('->__toString()', function () {

            it('should return []', function () {

                expect((string) new ArrayStr([]))->toEqual('[]');

            });

        });

    });

    context('when the array is not empty', function () {

        context('when all the values of the array are strings', function () {

            context('when the array has no string key', function () {

                describe('->__toString()', function () {

                    it('should return a string representation of the array with no key', function () {

                        expect((string) new ArrayStr(['v1', 'v2', 'v3']))->toEqual(<<<'EOT'
[
    v1,
    v2,
    v3,
]
EOT
                        );

                    });

                });

            });

            context('when the array has string keys', function () {

                describe('->__toString()', function () {

                    it('should return a string representation of the array with the keys', function () {

                        expect((string) new ArrayStr(['k0' => 'v1', 'v2', 'k1' => 'v3']))->toEqual(<<<'EOT'
[
    'k0' => v1,
    0 => v2,
    'k1' => v3,
]
EOT
                        );

                    });

                });

            });

        });

        context('when a value of the array is not a string', function () {

            it('should throw an InvalidArgumentException', function () {

                $test = function () {
                    new ArrayStr([
                        'key1' => 'value1',
                        'key2' => 2,
                        'key3' => 'value3',
                    ]);
                };

                expect($test)->toThrow(new InvalidArgumentException);

            });

        });

    });

});
