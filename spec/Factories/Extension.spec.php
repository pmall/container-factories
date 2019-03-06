<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Compiler;
use Quanta\Container\Factories\Extension;
use Quanta\Container\Factories\ClosureCompilerInterface;
use Quanta\Container\Factories\CompilableFactoryInterface;

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

        $this->factory1 = function (ContainerInterface $container) {
            return implode(':', [get_class($container), 'factory1']);
        };

        $this->factory2 = function (ContainerInterface $container, string $previous) {
            return implode(':', [$previous, get_class($container), 'factory2']);
        };

        $this->factory = new Extension($this->factory1, $this->factory2);

    });

    it('should implement CompilableFactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(CompilableFactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should return the value produced by the extension with the container and the value produced by the factory with the container', function () {

            $container = mock(ContainerInterface::class);

            $test = ($this->factory)($container->get());

            expect($test)->toEqual(implode(':', [
                get_class($container->get()), 'factory1',
                get_class($container->get()), 'factory2',
            ]));

        });

    });

    describe('->compiled()', function () {

        it('should return a string representation of the extension', function () {

            $delegate = mock(ClosureCompilerInterface::class);

            $compiler = new Compiler($delegate->get());

            $delegate->__invoke
                ->with(Kahlan\Arg::toBe($this->factory1))
                ->returns('factory');

            $delegate->__invoke
                ->with(Kahlan\Arg::toBe($this->factory2))
                ->returns('extension');

            $test = (string) $this->factory->compiled($compiler);

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    return (extension)($container, (factory)($container));
}
EOT
            );

        });

    });

});
