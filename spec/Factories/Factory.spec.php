<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Values\ValueInterface;

use Quanta\Container\Factories\Factory;
use Quanta\Container\Factories\Compiler;
use Quanta\Container\Factories\CompilableFactoryInterface;

describe('Factory', function () {

    beforeEach(function () {

        $this->value = mock(ValueInterface::class);

        $this->factory = new Factory($this->value->get());

    });

    it('should implement CompilableFactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(CompilableFactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should return the value returned by the ValueInterface implementation ->value() method', function () {

            $container = mock(ContainerInterface::class);

            $this->value->value->with($container)->returns('value');

            $test = ($this->factory)($container->get());

            expect($test)->toEqual('value');

        });

    });

    describe('->compiled()', function () {

        it('should return the string representation of the factory', function () {

            $compiler = Compiler::withDummyClosureCompiler();

            $this->value->str->with('container')->returns('\'value\'');

            $test = (string) $this->factory->compiled($compiler);

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    return 'value';
}
EOT
            );

        });

    });

});
