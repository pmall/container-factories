<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Invokable;
use Quanta\Container\FactoryInterface;
use Quanta\Container\Compilation\Compiler;

require_once __DIR__ . '/.test/classes.php';

describe('Invokable::instance()', function () {

    it('should return a new Invokable with the given class name', function () {

        $test = Invokable::instance(Test\TestInvokable::class);

        expect($test)->toEqual(new Invokable(Test\TestInvokable::class));

    });

});

describe('Invokable', function () {

    beforeEach(function () {

        $this->factory = new Invokable(Test\TestInvokable::class);

    });

    it('should implement FactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should instantiate the invokable class and proxy it', function () {

            $container = mock(ContainerInterface::class);

            $container->get->with('id')->returns('value');

            $test = ($this->factory)($container->get());

            expect($test)->toEqual('value');

        });

    });

    describe('->compilable()', function () {

        it('should return a compilable version of the invokable', function () {

            $compiler = Compiler::testing();

            $test = $this->factory->compilable('container');

            expect($compiler($test))->toEqual('(new Test\TestInvokable)($container)');

        });

    });

});
