<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Invokable;
use Quanta\Container\Factories\CompilableFactoryInterface;

use Quanta\Container\Compilation\Template;

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

            $template = Template::withDummyClosureCompiler('container_var_name');

            $test = $this->factory->compiled($template);

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container_var_name) {
    return (new \Test\TestInvokable)($container_var_name);
}
EOT
            );

        });

    });

});
