<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\DefinitionProxy;
use Quanta\Container\FactoryInterface;
use Quanta\Container\DefinitionInterface;
use Quanta\Container\Compilation\CompilableInterface;

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

    describe('->compilable()', function () {

        it('should proxy the factory provided by the definition', function () {

            $factory = mock(FactoryInterface::class);

            $compilable = mock(CompilableInterface::class);

            $this->definition->factory->returns($factory);

            $factory->compilable->with('container')->returns($compilable);

            $test = $this->factory->compilable('container');

            expect($test)->toBe($compilable->get());

        });

    });

});
