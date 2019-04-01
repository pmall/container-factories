<?php

use function Eloquent\Phony\Kahlan\stub;
use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Extension;
use Quanta\Container\FactoryInterface;
use Quanta\Container\Compilation\Compiler;
use Quanta\Container\Compilation\Template;
use Quanta\Container\Compilation\CompilableFactory;

describe('Extension::instance()', function () {

    it('should return a new Extension with the given factory and extension', function () {

        $factory = function () {};
        $extension = function () {};

        $test = Extension::instance($factory, $extension);

        expect($test)->toEqual(new Extension($factory, $extension));

    });

});

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

    describe('->compilable()', function () {

        it('should return a compilable version of the extension', function () {

            $compiler = Compiler::testing([
                'factory1' => $this->factory1,
                'factory2' => $this->factory2,
            ]);

            $test = $this->factory->compilable('container');

            expect($test)->toEqual(new Template('(%s)($container, (%s)($container))', ...[
                new CompilableFactory($this->factory2),
                new CompilableFactory($this->factory1),
            ]));

            expect($compiler($test))->toEqual('(factory2)($container, (factory1)($container))');

        });

    });

});
