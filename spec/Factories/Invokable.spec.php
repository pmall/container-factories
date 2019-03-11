<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Compiler;
use Quanta\Container\Factories\Invokable;
use Quanta\Container\Factories\DummyClosureCompiler;
use Quanta\Container\Factories\CompilableFactoryInterface;

require_once __DIR__ . '/../.test/classes.php';

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

        it('should return the string representation of the invokable', function () {

            $compiler = new Compiler(new DummyClosureCompiler);

            $test = (string) $this->factory->compiled($compiler);

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    return (new \Test\TestInvokable)($container);
}
EOT
            );

        });

    });

});
