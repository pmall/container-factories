<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Parameter;
use Quanta\Container\Factories\CompilableFactoryInterface;

use Quanta\Container\Values\ValueInterface;

use Quanta\Container\Compilation\Template;

describe('Parameter', function () {

    beforeEach(function () {

        $this->value = mock(ValueInterface::class);

        $this->factory = new Parameter($this->value->get());

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

        it('should return the string representation of a factory returning the return value of the ValueInterface ->str() method', function () {

            $template = Template::withDummyClosureCompiler('container_var_name');

            $this->value->str->with('container_var_name')->returns('\'value\'');

            $test = $this->factory->compiled($template);

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container_var_name) {
    return 'value';
}
EOT
            );

        });

    });

});
