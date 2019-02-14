<?php

use Quanta\Container\Compilation\DummyClosureCompiler;

describe('DummyClosureCompiler', function () {

    describe('->compiled()', function () {

        it('should throw an exception', function () {

            $compiler = new DummyClosureCompiler;

            $test = function () use ($compiler) {
                $compiler->compiled(function () {});
            };

            expect($test)->toThrow();

        });

    });

});
