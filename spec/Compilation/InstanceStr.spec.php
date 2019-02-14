<?php

use Quanta\Container\Compilation\InstanceStr;

describe('IndentedStr', function () {

    context('when there is no argument', function () {

        context('->__toString()', function () {

            it('should return a string representation of the instance with no argument', function () {

                $test = (string) new InstanceStr(Test\SomeClass::class);

                expect($test)->toEqual('new \Test\SomeClass');

            });

        });

    });

    context('when there is arguments', function () {

        context('->__toString()', function () {

            it('should return a string representation of the instance with the arguments', function () {

                $test = (string) new InstanceStr(Test\SomeClass::class, ...[
                    'v1',
                    'v2',
                    'v3',
                ]);

                expect($test)->toEqual(<<<'EOT'
new \Test\SomeClass(
    v1,
    v2,
    v3
)
EOT
                );

            });

        });

    });

});
