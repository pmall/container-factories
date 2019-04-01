<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Alias;
use Quanta\Container\FactoryInterface;
use Quanta\Container\Compilation\Compiler;

describe('Alias::instance()', function () {

    it('should return a new Alias with the given id', function () {

        $test = Alias::instance('id');

        expect($test)->toEqual(new Alias('id'));

    });

});

describe('Alias', function () {

    beforeEach(function () {

        $this->factory = new Alias('id');

    });

    it('should implement FactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should return the container entry associated with the id', function () {

            $container = mock(ContainerInterface::class);

            $container->get->with('id')->returns('value');

            $test = ($this->factory)($container->get());

            expect($test)->toEqual('value');

        });

    });

    describe('->compilable()', function () {

        it('should return a compilable version of the alias', function () {

            $compiler = Compiler::testing();

            $test = $this->factory->compilable('container');

            expect($compiler($test))->toEqual('$container->get(\'id\')');

        });

    });

});
