<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Extension;
use Quanta\Container\Factories\CompilableFactoryInterface;

use Quanta\Container\Compilation\Template;
use Quanta\Container\Compilation\ClosureCompilerInterface;

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

            $compiler = mock(ClosureCompilerInterface::class);

            $template = Template::withClosureCompiler(...[
                $compiler->get(),
                'container_var_name',
            ]);

            $compiler->compiled
                ->with(Kahlan\Arg::toBe($this->factory1))
                ->returns('factory');

            $compiler->compiled
                ->with(Kahlan\Arg::toBe($this->factory2))
                ->returns('extension');

            $test = $this->factory->compiled($template);

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container_var_name) {
    $factory = factory;
    $extension = extension;
    return ($extension)($container_var_name, ($factory)($container_var_name));
}
EOT
            );

        });

    });

});
