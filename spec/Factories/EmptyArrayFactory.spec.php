<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Compiler;
use Quanta\Container\Factories\EmptyArrayFactory;
use Quanta\Container\Factories\CompilableFactoryInterface;

describe('EmptyArrayFactory', function () {

    beforeEach(function () {

        $this->factory = new EmptyArrayFactory;

    });

    it('should implement CompilableFactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(CompilableFactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should return an empty array', function () {

            $container = mock(ContainerInterface::class);

            $test = ($this->factory)($container->get());

            expect($test)->toEqual([]);

        });

    });

    describe('->compiled()', function () {

        it('should return a string representation of a factory returning an empty array', function () {

            $compiler = Compiler::withDummyClosureCompiler();

            $test = (string) $this->factory->compiled($compiler);

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    return [];
}
EOT
            );

        });

    });

});
