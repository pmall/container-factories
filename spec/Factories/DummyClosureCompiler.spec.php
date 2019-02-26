<?php

use Quanta\Container\Factories\DummyClosureCompiler;

describe('DummyClosureCompiler', function () {

    describe('->__invoke()', function () {

        it('should throw an exception', function () {

            $compiler = new DummyClosureCompiler;

            $test = function () use ($compiler) {
                $compiler(function () {});
            };

            expect($test)->toThrow();

        });

    });

});
