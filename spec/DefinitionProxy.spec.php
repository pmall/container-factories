<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\DefinitionProxy;
use Quanta\Container\FactoryInterface;
use Quanta\Container\DefinitionInterface;

describe('DefinitionProxy', function () {

    beforeEach(function () {

        $this->definition = mock(DefinitionInterface::class);

        $this->factory = new DefinitionProxy($this->definition->get());

    });

    it('should implement FactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should proxy the factory provided by the definition', function () {

            $container = mock(ContainerInterface::class);

            $factory = mock(FactoryInterface::class);

            $this->definition->factory->returns($factory);

            $factory->__invoke->with($container)->returns('value');

            $test = ($this->factory)($container->get());

            expect($test)->toEqual('value');

        });

    });

    describe('->compiled()', function () {

        it('should proxy the factory provided by the definition', function () {

            $compiler = function () {};

            $factory = mock(FactoryInterface::class);

            $this->definition->factory->returns($factory);

            $factory->compiled
                ->with('container', Kahlan\Arg::toBe($compiler))
                ->returns('value');

            $test = $this->factory->compiled('container', $compiler);

            expect($test)->toEqual('value');

        });

    });

});
