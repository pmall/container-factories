<?php

use Quanta\Container\Compilation\StaticMethodStr;

describe('StaticMethodStr', function () {

    describe('->__toString()', function () {

        context('when the class name is not prepended with \\', function () {

            it('should return string representation of the static method', function () {

                $test = (string) new StaticMethodStr(Test\TestClass::class, 'method');

                expect($test)->toEqual('[\Test\TestClass::class, \'method\']');

            });

        });

        context('when the class name is prepended with \\', function () {

            it('should return string representation of the static method', function () {

                $test = (string) new StaticMethodStr(\Test\TestClass::class, 'method');

                expect($test)->toEqual('[\Test\TestClass::class, \'method\']');

            });

        });

    });

});
