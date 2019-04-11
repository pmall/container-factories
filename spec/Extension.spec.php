<?php

use function Eloquent\Phony\Kahlan\stub;
use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Extension;
use Quanta\Container\FactoryInterface;

describe('Extension', function () {

    beforeEach(function () {

        $this->factory1 = stub();
        $this->factory2 = stub();

        $this->factory = new Extension($this->factory1, $this->factory2);

    });

    it('should implement FactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(FactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should return the value produced by the extension', function () {

            $container = mock(ContainerInterface::class);

            $this->factory1->with($container)->returns('value1');
            $this->factory2->with($container, 'value1')->returns('value2');

            $test = ($this->factory)($container->get());

            expect($test)->toEqual('value2');

        });

    });

    describe('->compiled()', function () {

        it('should return a compiled version of the extension', function () {

            $compiler = stub();

            $compiler->with($this->factory1)->returns('factory1');
            $compiler->with($this->factory2)->returns('factory2');

            $test = $this->factory->compiled('container', $compiler);

            expect($test)->toEqual('(factory2)($container, (factory1)($container))');

        });

    });

});
