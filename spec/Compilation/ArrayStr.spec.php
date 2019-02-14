<?php

use Quanta\Container\Compilation\ArrayStr;

use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

describe('ArrayStr', function () {

    context('when all the values of the array are strings', function () {

        context('when the array is empty', function () {

            describe('->__toString()', function () {

                it('should return []', function () {

                    expect((string) new ArrayStr([]))->toEqual('[]');

                });

            });

        });

        context('when the array is not empty', function () {

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

    });

    context('when a value of the array is not a string', function () {

        it('should throw an InvalidArgumentException', function () {

            ArrayArgumentTypeErrorMessage::testing();

            $values = ['value1', 2, 'value3'];

            $test = function () use ($values) { new ArrayStr($values); };

            expect($test)->toThrow(new InvalidArgumentException(
                (string) new ArrayArgumentTypeErrorMessage(1, 'string', $values)
            ));

        });

    });

});
