<?php

use Quanta\Container\Compilation\DummyClosureCompiler;
use Quanta\Container\Compilation\ClosureCompilerInterface;

describe('DummyClosureCompiler', function () {

    beforeEach(function () {

        $this->compiler = new DummyClosureCompiler;

    });

    it('should implement ClosureCompilerInterface', function () {

        expect($this->compiler)->toBeAnInstanceOf(ClosureCompilerInterface::class);

    });

    describe('->__invoke()', function () {

        it('should throw an exception', function () {

            $test = function () {
                ($this->compiler)(function () {});
            };

            expect($test)->toThrow();

        });

    });

});
