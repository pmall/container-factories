<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Alias;
use Quanta\Container\Factories\Compiler;
use Quanta\Container\Factories\CompilableFactoryInterface;

describe('Alias', function () {

    beforeEach(function () {

        $this->factory = new Alias('id');

    });

    it('should implement CompilableFactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(CompilableFactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should return the container entry associated with the id', function () {

            $container = mock(ContainerInterface::class);

            $container->get->with('id')->returns('value');

            $test = ($this->factory)($container->get());

            expect($test)->toEqual('value');

        });

    });

    describe('->compiled()', function () {

        it('should return a string representation of the alias', function () {

            $compiler = Compiler::withDummyClosureCompiler();

            $test = $this->factory->compiled($compiler);

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    return $container->get('id');
}
EOT
            );

        });

    });

});
