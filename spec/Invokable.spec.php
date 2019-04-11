<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Invokable;
use Quanta\Container\FactoryInterface;

require_once __DIR__ . '/.test/classes.php';

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

    describe('->compiled()', function () {

        it('should return a compiled version of the invokable', function () {

            $test = $this->factory->compiled('container', function () {});

            expect($test)->toEqual('(new Test\TestInvokable)($container)');

        });

    });

});
