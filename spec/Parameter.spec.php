<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Parameter;
use Quanta\Container\FactoryInterface;
use Quanta\Container\Parsing\ParserInterface;
use Quanta\Container\Compilation\Compiler;

describe('Factory', function () {

    beforeEach(function () {

        $this->factory = new Parameter('value');

    });

    it('should implement FactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should return the value', function () {

            $container = mock(ContainerInterface::class);

            $test = ($this->factory)($container->get());

            expect($test)->toEqual('value');

        });

    });

    describe('->compilable()', function () {

        it('should return a compilable version of the value', function () {

            $compiler = Compiler::testing([
                'compiled' => 'value',
            ]);

            $test = $this->factory->compilable('container');

            expect($compiler($test))->toEqual('compiled');

        });

    });

});
