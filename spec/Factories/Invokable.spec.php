<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Compiler;
use Quanta\Container\Factories\Invokable;
use Quanta\Container\Factories\CompilableFactoryInterface;

require_once __DIR__ . '/../.test/classes.php';

describe('Invokable', function () {

    beforeEach(function () {

        $this->factory = new Invokable(Test\TestInvokable::class);

    });

    it('should implement CompilableFactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(CompilableFactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should instantiate the invokable class and proxy it', function () {

            $container = mock(ContainerInterface::class);

            Test\TestInvokable::setup($container->get(), 'value');

            $test = ($this->factory)($container->get());

            expect($test)->toEqual('value');

        });

    });

    describe('->compiled()', function () {

        it('should return the string representation of a factory instantiating the invokable class and proxying it', function () {

            $compiler = Compiler::withDummyClosureCompiler();

            $test = $this->factory->compiled($compiler);

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    return (new \Test\TestInvokable)($container);
}
EOT
            );

        });

    });

});
